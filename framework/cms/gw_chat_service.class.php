<?php

class GW_Chat_Service
{
	static $instance;

	const DEFAULT_ALLOWED_ATTACHMENT_EXTENSIONS = 'pdf,doc,docx,docm,dot,dotx,odt,ott,rtf,txt,csv,xls,xlsx,xlsm,ods,ots,ppt,pptx,pptm,odp,otp,jpg,jpeg,png,gif,webp,bmp,tif,tiff,heic,heif,zip,rar,7z';
	const DEFAULT_ATTACHMENT_SIZE_MB = 10;
	const ATTACHMENT_STORAGE_LOCAL = 'local';
	const ATTACHMENT_STORAGE_VORO1 = 'voro1';
	const PUSH_QUEUE_GROUP = 'chat_push_queue';
	const PUSH_SENT_GROUP = 'chat_push_sent';

	protected $reactOnlineUserIdsCache = null;

	public static function singleton()
	{
		if (!self::$instance)
			self::$instance = new self();

		return self::$instance;
	}

	function now()
	{
		return date('Y-m-d H:i:s');
	}

	function chatConfig($key, $default = null)
	{
		$value = GW_Config::singleton()->get('users__chat/' . $key);
		return ($value === null || $value === '') ? $default : $value;
	}

	function chatConfigInt($key, $default = 0)
	{
		$value = $this->chatConfig($key, $default);
		return is_numeric($value) ? (int)$value : (int)$default;
	}

	function chatConfigBool($key, $default = false)
	{
		$value = $this->chatConfig($key, $default ? 1 : 0);
		return in_array(strtolower((string)$value), ['1', 'true', 'yes', 'on'], true);
	}

	function normalizeEventPayload($payload)
	{
		if (is_array($payload))
			return $payload;

		if (!$payload)
			return [];

		$tmp = json_decode((string)$payload, true);
		return is_array($tmp) ? $tmp : [];
	}

	function createRoomEvent($roomId, $userId, $eventType, $refId = 0, $payload = [], $time = '')
	{
		$time = $time ?: $this->now();
		$event = GW_Chat_Event::singleton()->createNewObject([
			'room_id' => (int)$roomId,
			'user_id' => (int)$userId,
			'event_type' => trim((string)$eventType),
			'ref_id' => (int)$refId,
			'payload_json' => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
			'insert_time' => $time,
		]);
		$event->insert();

		return $event;
	}

	function touchRoomActivity($room, $time, $eventId = 0, $messageId = null, $messageTime = null)
	{
		if (!$room)
			return;

		$values = [
			'update_time' => $time,
		];

		if ($eventId > 0) {
			$values['last_event_id'] = (int)$eventId;
			$values['last_event_time'] = $time;
		}

		if ($messageId !== null) {
			$values['last_message_id'] = (int)$messageId;
			$values['last_message_time'] = $messageTime ?: $time;
		}

		$room->saveValues($values);
	}

	function buildUserEventPayload($user, $extra = [])
	{
		return [
			'user' => [
				'id' => (int)($user->id ?? 0),
				'username' => (string)($user->username ?? ''),
				'name' => $this->getUserDisplayName($user),
			],
		] + $extra;
	}

	function eventToTimelineEntry($row)
	{
		$payload = $this->normalizeEventPayload($row['payload_json'] ?? null);
		$user = $payload['user'] ?? [];
		$userName = trim((string)($user['name'] ?? '')) ?: ((int)($row['user_id'] ?? 0) ? 'User #'.(int)$row['user_id'] : 'System');
		$eventType = (string)($row['event_type'] ?? '');
		$text = '';

		if ($eventType === 'join')
			$text = $userName.' joined the room';
		elseif ($eventType === 'leave')
			$text = $userName.' left the room';
		elseif ($eventType === 'reaction_add')
			$text = $userName.' reacted '.trim((string)($payload['reaction'] ?? ''));
		elseif ($eventType === 'reaction_remove')
			$text = $userName.' removed reaction '.trim((string)($payload['reaction'] ?? ''));
		elseif ($eventType === 'reaction_change')
			$text = $userName.' changed reaction to '.trim((string)($payload['reaction'] ?? ''));
		else
			$text = $userName.' updated room activity';

		return [
			'entry_type' => 'event',
			'entry_key' => 'e'.(int)$row['id'],
			'event_id' => (int)$row['id'],
			'room_id' => (int)$row['room_id'],
			'user_id' => (int)$row['user_id'],
			'event_type' => $eventType,
			'ref_id' => (int)($row['ref_id'] ?? 0),
			'text' => trim($text),
			'payload' => $payload,
			'insert_time' => $this->normalizeDateValue($row['insert_time'] ?? ''),
		];
	}

	function buildEventPacket($event)
	{
		if (!$event)
			return null;

		$row = is_array($event) ? $event : $event->toArray();
		$entry = $this->eventToTimelineEntry($row);

		return [
			'action' => 'chat_event',
			'room_id' => (int)$entry['room_id'],
			'event' => $entry,
		];
	}

	function buildDirectKey($userId1, $userId2)
	{
		$ids = [(int)$userId1, (int)$userId2];
		sort($ids, SORT_NUMERIC);

		return $ids[0].':'.$ids[1];
	}

	function getUserDisplayName($user)
	{
		if (!$user)
			return '';

		$name = trim(($user->name ?? '').' '.($user->surname ?? ''));

		return $name ?: $user->username;
	}

	function normalizeDateValue($value)
	{
		return ($value === '0000-00-00 00:00:00' || $value === '0000-00-00' || $value === null) ? '' : $value;
	}

	function getRoom($roomId)
	{
		return GW_Chat_Room::singleton()->find(['id=?', (int)$roomId]);
	}

	function getMembership($roomId, $userId, $activeOnly = false)
	{
		$cond = 'room_id='.(int)$roomId.' AND user_id='.(int)$userId;

		if ($activeOnly)
			$cond .= ' AND is_active=1';

		return GW_Chat_Room_User::singleton()->find($cond);
	}

	function ensureRoomAccess($roomId, $userId, $activeOnly = true)
	{
		$membership = $this->getMembership($roomId, $userId, $activeOnly);

		if (!$membership)
			throw new Exception('No access to room');

		return $membership;
	}

	function ensureUserExists($userId)
	{
		$user = GW_User::singleton()->find(['id=?', (int)$userId]);

		if (!$user)
			throw new Exception('User not found');

		return $user;
	}

	function ensureMembership($roomId, $userId, $role = 'member')
	{
		$membership = $this->getMembership($roomId, $userId, false);
		$time = $this->now();

		if ($membership) {
			$membership->saveValues([
				'role' => $role ?: $membership->role,
				'is_active' => 1,
				'update_time' => $time
			]);
		} else {
			$membership = GW_Chat_Room_User::singleton()->createNewObject([
				'room_id' => (int)$roomId,
				'user_id' => (int)$userId,
				'role' => $role ?: 'member',
				'is_active' => 1,
				'last_seen_message_id' => 0,
				'last_seen_event_id' => 0,
				'last_seen_time' => null,
				'insert_time' => $time,
				'update_time' => $time
			]);

			$membership->insert();
		}

		return $membership;
	}

	function getRoomUsers($roomId, $activeOnly = true)
	{
		$cond = ['room_id=?', (int)$roomId];

		if ($activeOnly)
			$cond[0] .= ' AND is_active=1';

		return GW_Chat_Room_User::singleton()->findAll($cond);
	}

	function getRoomUserIds($roomId, $activeOnly = true)
	{
		$list = $this->getRoomUsers($roomId, $activeOnly);
		$ids = [];

		foreach ($list as $item)
			$ids[] = (int)$item->user_id;

		return $ids;
	}

	function getUsersIndexed($userIds)
	{
		$userIds = array_values(array_unique(array_map('intval', $userIds)));

		if (!$userIds)
			return [];

		return GW_User::singleton()->findAll(GW_DB::inCondition('id', $userIds), ['key_field' => 'id']);
	}

	function normalizeReaction($reaction)
	{
		$reaction = trim((string)$reaction);
		$allowed = ['❤️', '👍', '😂', '😮', '😢', '🔥'];

		if (!in_array($reaction, $allowed, true))
			throw new Exception('Invalid reaction');

		return $reaction;
	}

	function getMessageById($messageId)
	{
		return GW_Chat_Message::singleton()->find(['id=?', (int)$messageId]);
	}

	function getMessageReactionsMap($messageIds, $viewerUserId = 0)
	{
		$messageIds = array_values(array_unique(array_filter(array_map('intval', (array)$messageIds))));

		if (!$messageIds)
			return [];

		$messageIdsSql = implode(',', $messageIds);

		$rows = GW::db()->fetch_rows("
			SELECT
				r.message_id,
				r.user_id,
				r.reaction,
				u.username,
				u.name,
				u.surname
			FROM gw_chat_message_reactions AS r
			LEFT JOIN gw_users AS u ON u.id = r.user_id
			WHERE r.message_id IN ($messageIdsSql)
			ORDER BY r.message_id ASC, r.id ASC
		");

		$out = [];

		foreach ($rows as $row) {
			$messageId = (int)$row['message_id'];
			$reaction = (string)$row['reaction'];
			$userId = (int)$row['user_id'];
			$userName = trim(($row['name'] ?? '').' '.($row['surname'] ?? '')) ?: (string)($row['username'] ?? ('User #'.$userId));

			if (!isset($out[$messageId]))
				$out[$messageId] = [];

			if (!isset($out[$messageId][$reaction])) {
				$out[$messageId][$reaction] = [
					'reaction' => $reaction,
					'count' => 0,
					'users' => [],
					'reacted_by_me' => 0,
				];
			}

			$out[$messageId][$reaction]['count']++;
			$out[$messageId][$reaction]['users'][] = [
				'id' => $userId,
				'name' => $userName,
				'username' => (string)($row['username'] ?? ''),
			];

			if ($viewerUserId && $userId === (int)$viewerUserId)
				$out[$messageId][$reaction]['reacted_by_me'] = 1;
		}

		foreach ($out as $messageId => $grouped)
			$out[$messageId] = array_values($grouped);

		return $out;
	}

	function getMessageReactions($messageId, $viewerUserId = 0)
	{
		$map = $this->getMessageReactionsMap([(int)$messageId], $viewerUserId);
		return $map[(int)$messageId] ?? [];
	}

	function getMessageAttachmentsMap($messageIds)
	{
		$messageIds = array_values(array_unique(array_filter(array_map('intval', (array)$messageIds))));
		if (!$messageIds)
			return [];

		$messageIdsSql = implode(',', $messageIds);
		$rows = GW::db()->fetch_rows("
			SELECT *
			FROM gw_chat_attachments
			WHERE message_id IN ($messageIdsSql)
			  AND is_deleted = 0
			ORDER BY id ASC
		");

		$out = [];
		foreach ($rows as $row) {
			$messageId = (int)$row['message_id'];
			if (!isset($out[$messageId]))
				$out[$messageId] = [];

			$out[$messageId][] = $this->attachmentRowToArray($row);
		}

		return $out;
	}

	function attachmentRowToArray($row)
	{
		$publicUrl = (string)$row['public_url'];
		$thumbUrl = (string)$row['thumb_url'];

		if (!empty($row['relpath'])) {
			$baseUrl = $this->attachmentBaseUrlFromStoredUrl($publicUrl) ?: $this->attachmentBaseUrlFromStoredUrl($thumbUrl) ?: null;
			[$builtPublicUrl, $builtThumbUrl] = $this->buildAttachmentUrls((string)$row['relpath'], (string)$row['kind'], $baseUrl, (string)$row['original_filename']);

			if ((string)$row['kind'] === 'image' || strpos((string)$row['relpath'], '.sys/chat_files/') === 0) {
				$publicUrl = $builtPublicUrl;
				$thumbUrl = $builtThumbUrl;
			} else {
				$publicUrl = $publicUrl ?: $builtPublicUrl;
				$thumbUrl = $thumbUrl ?: $builtThumbUrl;
			}
		}

		return [
			'id' => (int)$row['id'],
			'message_id' => (int)$row['message_id'],
			'room_id' => (int)$row['room_id'],
			'uploader_id' => (int)$row['uploader_id'],
			'storage' => (string)$row['storage'],
			'kind' => (string)$row['kind'],
			'original_filename' => (string)$row['original_filename'],
			'stored_filename' => (string)$row['stored_filename'],
			'relpath' => (string)$row['relpath'],
			'mime' => (string)$row['mime'],
			'size' => (int)$row['size'],
			'size_human' => GW_Math_Helper::cFileSize((int)$row['size']),
			'public_url' => $publicUrl,
			'thumb_url' => $thumbUrl,
		];
	}

	function roomToArray($room, $viewerUserId = 0)
	{
		$data = $room->toArray();
		$data['last_message_time'] = $this->normalizeDateValue($data['last_message_time'] ?? '');
		$data['last_event_id'] = (int)($data['last_event_id'] ?? 0);
		$data['last_event_time'] = $this->normalizeDateValue($data['last_event_time'] ?? '');
		$data['insert_time'] = $this->normalizeDateValue($data['insert_time'] ?? '');
		$data['update_time'] = $this->normalizeDateValue($data['update_time'] ?? '');
		$data['members'] = [];

		$memberIds = $this->getRoomUserIds($room->id, true);
		$users = $this->getUsersIndexed($memberIds);

		foreach ($memberIds as $userId) {
			$user = $users[$userId] ?? null;

			if (!$user)
				continue;

			$data['members'][] = [
				'id' => (int)$user->id,
				'username' => $user->username,
				'title' => $this->getUserDisplayName($user)
			];
		}

		if ($room->type == 'private') {
			foreach ($data['members'] as $member) {
				if ((int)$member['id'] !== (int)$viewerUserId) {
					$data['display_title'] = $member['title'];
					$data['display_user_id'] = $member['id'];
					break;
				}
			}
		} else {
			$data['display_title'] = $room->title;
			$data['display_user_id'] = 0;
		}

		return $data;
	}

	function getMyRooms($userId)
	{
		$userId = (int)$userId;
		$rows = GW::db()->fetch_rows("
			SELECT
				r.*,
				ru.last_seen_message_id,
				ru.last_seen_event_id,
				ru.last_seen_time,
				(
					SELECT COUNT(*)
					FROM gw_chat_messages AS m2
					WHERE m2.room_id = r.id
					  AND m2.id > ru.last_seen_message_id
					  AND m2.is_deleted = 0
					  AND m2.sender_id != $userId
				) AS unread_count
				,
				(
					SELECT COUNT(*)
					FROM gw_chat_events AS e2
					WHERE e2.room_id = r.id
					  AND e2.id > ru.last_seen_event_id
					  AND e2.user_id != $userId
					  AND e2.event_type != 'message'
				) AS unread_activity_count
			FROM gw_chat_room_users AS ru
			INNER JOIN gw_chat_rooms AS r ON r.id = ru.room_id
			WHERE ru.user_id = $userId
			  AND ru.is_active = 1
			  AND r.is_active = 1
			ORDER BY COALESCE(r.last_event_time, r.last_message_time, r.update_time) DESC, r.id DESC
		");

		$list = [];

		foreach ($rows as $row) {
			$room = GW_Chat_Room::singleton()->createNewObject($row);
			$tmp = $this->roomToArray($room, $userId);
			$tmp['unread_count'] = (int)$row['unread_count'];
			$tmp['unread_activity_count'] = (int)$row['unread_activity_count'];
			$tmp['last_seen_message_id'] = (int)$row['last_seen_message_id'];
			$tmp['last_seen_event_id'] = (int)$row['last_seen_event_id'];
			$tmp['last_seen_time'] = $row['last_seen_time'];
			$list[] = $tmp;
		}

		return $list;
	}

	function getRoomInfo($roomId, $userId)
	{
		$this->ensureRoomAccess($roomId, $userId, false);
		$room = $this->getRoom($roomId);

		if (!$room)
			throw new Exception('Room not found');

		return $this->roomToArray($room, $userId);
	}

	function getOrCreatePrivateRoom($userId1, $userId2)
	{
		$userId1 = (int)$userId1;
		$userId2 = (int)$userId2;

		if ($userId1 <= 0 || $userId2 <= 0 || $userId1 == $userId2)
			throw new Exception('Invalid private room users');

		$this->ensureUserExists($userId1);
		$this->ensureUserExists($userId2);

		$directKey = $this->buildDirectKey($userId1, $userId2);
		$room = GW_Chat_Room::singleton()->find(['direct_key=?', $directKey]);
		$time = $this->now();

		if (!$room) {
			$room = GW_Chat_Room::singleton()->createNewObject([
				'type' => 'private',
				'title' => '',
				'direct_key' => $directKey,
				'creator_id' => $userId1,
				'is_active' => 1,
				'room_history_limit' => null,
				'last_message_id' => 0,
				'last_message_time' => null,
				'last_event_id' => 0,
				'last_event_time' => null,
				'insert_time' => $time,
				'update_time' => $time
			]);

			$room->insert();
		} else {
			$room->saveValues([
				'is_active' => 1,
				'update_time' => $time
			]);
		}

		$this->ensureMembership($room->id, $userId1, 'owner');
		$this->ensureMembership($room->id, $userId2, 'member');

		return $room;
	}

	function createGroupRoom($creatorId, $title, $userIds = [], $roomHistoryLimit = 1000)
	{
		$creatorId = (int)$creatorId;
		$this->ensureUserExists($creatorId);

		$userIds[] = $creatorId;
		$userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));

		foreach ($userIds as $userId)
			$this->ensureUserExists($userId);

		$roomHistoryLimit = (int)$roomHistoryLimit;
		if ($roomHistoryLimit <= 0)
			$roomHistoryLimit = null;

		$time = $this->now();
		$room = GW_Chat_Room::singleton()->createNewObject([
			'type' => 'group',
			'title' => trim($title),
			'direct_key' => null,
			'creator_id' => $creatorId,
			'is_active' => 1,
			'room_history_limit' => $roomHistoryLimit,
			'last_message_id' => 0,
			'last_message_time' => null,
			'last_event_id' => 0,
			'last_event_time' => null,
			'insert_time' => $time,
			'update_time' => $time
		]);

		$room->insert();

		foreach ($userIds as $userId)
			$this->ensureMembership($room->id, $userId, $userId == $creatorId ? 'owner' : 'member');

		return $room;
	}

	function joinRoom($roomId, $userId)
	{
		$room = $this->getRoom($roomId);

		if (!$room || !$room->is_active)
			throw new Exception('Room not found');

		$membership = $this->getMembership($roomId, $userId, false);
		$wasActive = $membership ? (int)$membership->is_active : 0;

		if (!$membership)
			throw new Exception('You are not invited to this room');

		$time = $this->now();
		$membership->saveValues([
			'is_active' => 1,
			'update_time' => $time
		]);

		$eventPacket = null;

		if (!$wasActive) {
			$user = $this->ensureUserExists($userId);
			$event = $this->createRoomEvent($roomId, $userId, 'join', 0, $this->buildUserEventPayload($user), $time);
			$this->touchRoomActivity($room, $time, $event->id);
			$eventPacket = $this->buildEventPacket($event);
		}

		return [
			'membership' => $membership,
			'did_join' => !$wasActive,
			'event_packet' => $eventPacket,
		];
	}

	function leaveRoom($roomId, $userId)
	{
		$room = $this->getRoom($roomId);
		$membership = $this->ensureRoomAccess($roomId, $userId, true);
		$time = $this->now();

		$membership->saveValues([
			'is_active' => 0,
			'update_time' => $time
		]);

		$user = $this->ensureUserExists($userId);
		$event = $this->createRoomEvent($roomId, $userId, 'leave', 0, $this->buildUserEventPayload($user), $time);
		$this->touchRoomActivity($room, $time, $event->id);

		return [
			'ok' => 1,
			'event_packet' => $this->buildEventPacket($event),
		];
	}

	function loadMessages($roomId, $userId, $beforeMessageId = 0, $limit = 50, $afterMessageId = 0)
	{
		$this->ensureRoomAccess($roomId, $userId, true);

		$roomId = (int)$roomId;
		$userId = (int)$userId;
		$beforeMessageId = (int)$beforeMessageId;
		$afterMessageId = (int)$afterMessageId;
		$limit = max(1, min(200, (int)$limit));
		$room = $this->getRoom($roomId);
		$peerSeenMessageId = 0;

		if ($room && $room->type == 'private') {
			$peerMembership = GW::db()->fetch_row("
				SELECT last_seen_message_id
				FROM gw_chat_room_users
				WHERE room_id = $roomId
				  AND user_id != $userId
				ORDER BY id ASC
				LIMIT 1
			");

			$peerSeenMessageId = (int)($peerMembership['last_seen_message_id'] ?? 0);
		}

		$cond = "m.room_id = $roomId AND m.is_deleted = 0";

		if ($afterMessageId > 0)
			$cond .= " AND m.id > $afterMessageId";
		elseif ($beforeMessageId > 0)
			$cond .= " AND m.id < $beforeMessageId";

		$order = $afterMessageId > 0 ? 'ASC' : 'DESC';
		$rows = GW::db()->fetch_rows("
			SELECT
				m.*,
				u.username,
				u.name,
				u.surname
			FROM gw_chat_messages AS m
			LEFT JOIN gw_users AS u ON u.id = m.sender_id
			WHERE $cond
			ORDER BY m.id $order
			LIMIT $limit
		");

		if ($afterMessageId <= 0)
			$rows = array_reverse($rows);
		$reactionsMap = $this->getMessageReactionsMap(array_column($rows, 'id'), $userId);
		$attachmentsMap = $this->getMessageAttachmentsMap(array_column($rows, 'id'));
		$list = [];
		$messageTimes = [];

		foreach ($rows as $row) {
			$messageId = (int)$row['id'];
			$messageTimes[] = $this->normalizeDateValue($row['insert_time']);
			$list[] = [
				'entry_type' => 'message',
				'entry_key' => 'm'.$messageId,
				'id' => $messageId,
				'room_id' => (int)$row['room_id'],
				'sender_id' => (int)$row['sender_id'],
				'sender_username' => $row['username'],
				'sender_title' => trim(($row['name'] ?? '').' '.($row['surname'] ?? '')) ?: $row['username'],
				'message' => $row['message'],
				'source' => $row['source'],
				'mentions_json' => $row['mentions_json'],
				'reply_to_message_id' => (int)$row['reply_to_message_id'],
				'is_seen' => ($room && $room->type == 'private' && (int)$row['sender_id'] === $userId && $messageId <= $peerSeenMessageId) ? 1 : 0,
				'reactions' => $reactionsMap[$messageId] ?? [],
				'attachments' => $attachmentsMap[$messageId] ?? [],
				'insert_time' => $this->normalizeDateValue($row['insert_time']),
				'update_time' => $this->normalizeDateValue($row['update_time'])
			];
		}

		$eventCond = ["room_id = $roomId AND event_type != 'message'"];

		if ($messageTimes) {
			$firstTime = GW::db()->escape($messageTimes[0]);
			$lastTime = GW::db()->escape($messageTimes[count($messageTimes) - 1]);
			$eventCond[] = "insert_time >= '$firstTime'";

			if ($afterMessageId > 0 || $beforeMessageId > 0)
				$eventCond[] = "insert_time <= '$lastTime'";
		} elseif ($beforeMessageId <= 0 && $afterMessageId <= 0) {
			$eventRows = GW::db()->fetch_rows("
				SELECT *
				FROM gw_chat_events
				WHERE room_id = $roomId
				  AND event_type != 'message'
				ORDER BY id DESC
				LIMIT ".max(10, min(50, $limit))."
			");
			$eventRows = array_reverse($eventRows);

			foreach ($eventRows as $row)
				$list[] = $this->eventToTimelineEntry($row);

			usort($list, function($a, $b){
				$keyA = ($a['insert_time'] ?? '').'|'.($a['entry_key'] ?? '');
				$keyB = ($b['insert_time'] ?? '').'|'.($b['entry_key'] ?? '');
				return strcmp($keyA, $keyB);
			});

			return $list;
		}

		if ($eventCond) {
			$eventRows = GW::db()->fetch_rows("
				SELECT *
				FROM gw_chat_events
				WHERE ".implode(' AND ', $eventCond)."
				ORDER BY id ASC
				LIMIT ".($limit * 3)."
			");

			foreach ($eventRows as $row)
				$list[] = $this->eventToTimelineEntry($row);
		}

		usort($list, function($a, $b){
			$keyA = ($a['insert_time'] ?? '').'|'.($a['entry_key'] ?? '');
			$keyB = ($b['insert_time'] ?? '').'|'.($b['entry_key'] ?? '');
			return strcmp($keyA, $keyB);
		});

		return $list;
	}

	function trimRoomHistoryIfNeeded($room)
	{
		if (!$room || $room->type != 'group')
			return;

		$limit = (int)$room->room_history_limit;

		if ($limit <= 0)
			return;

		$roomId = (int)$room->id;

		GW::db()->query("
			DELETE FROM gw_chat_messages
			WHERE room_id = $roomId
			  AND id NOT IN (
				SELECT id FROM (
					SELECT id
					FROM gw_chat_messages
					WHERE room_id = $roomId
					ORDER BY id DESC
					LIMIT $limit
				) AS x
			  )
		");
	}

	function notifyUsers($userIds, $packet)
	{
		$users = $this->getUsersIndexed($userIds);

		foreach ($users as $user) {
			if (!$user || !$user->username)
				continue;

			try {
				GW_WebSocket_Helper2::notifyUser($user->username, $packet);
			} catch (Throwable $e) {
				error_log('GW_Chat_Service notifyUsers failed for user '.$user->id.': '.$e->getMessage());
			}
		}
	}

	function notifyRoom($roomId, $packet, $excludeUserId = 0)
	{
		$userIds = $this->getRoomUserIds($roomId, true);

		if ($excludeUserId)
			$userIds = array_values(array_diff($userIds, [(int)$excludeUserId]));

		$this->notifyUsers($userIds, $packet);
	}

	function getReactOnlineUserIds()
	{
		if ($this->reactOnlineUserIdsCache !== null)
			return $this->reactOnlineUserIdsCache;

		$this->reactOnlineUserIdsCache = [];

		if (!GW_WebSocket_Helper2::enabled())
			return $this->reactOnlineUserIdsCache;

		$url = GW_WebSocket_Helper2::controlUrl('/healthz') . '?full=1';
		if (!$url)
			return $this->reactOnlineUserIdsCache;

		$ctx = stream_context_create([
			'http' => [
				'timeout' => 0.25,
				'ignore_errors' => true,
			],
		]);
		$body = @file_get_contents($url, false, $ctx);
		$data = is_string($body) ? json_decode($body, true) : null;
		$list = is_array($data) ? ($data['online_users'] ?? []) : [];

		foreach ((array)$list as $item) {
			$userId = (int)($item['id'] ?? 0);
			if ($userId > 0)
				$this->reactOnlineUserIdsCache[$userId] = 1;
		}

		return $this->reactOnlineUserIdsCache;
	}

	function isUserWsOnline($userId)
	{
		$userId = (int)$userId;
		$ids = $this->getReactOnlineUserIds();
		return !empty($ids[$userId]);
	}

	function isQuietHoursNow()
	{
		if (!$this->chatConfigBool('push_private_quiet_hours_enabled', false))
			return false;

		$from = trim((string)$this->chatConfig('push_private_quiet_hours_from', '22:00'));
		$to = trim((string)$this->chatConfig('push_private_quiet_hours_to', '08:00'));
		if (!preg_match('/^\d{1,2}:\d{2}$/', $from) || !preg_match('/^\d{1,2}:\d{2}$/', $to))
			return false;

		$now = (int)date('Hi');
		$fromInt = (int)str_replace(':', '', $from);
		$toInt = (int)str_replace(':', '', $to);

		if ($fromInt === $toInt)
			return false;

		return $fromInt < $toInt ? ($now >= $fromInt && $now < $toInt) : ($now >= $fromInt || $now < $toInt);
	}

	function pushCooldownName($roomId)
	{
		return 'room:' . (int)$roomId . ':last_sent_at';
	}

	function pushQueueName($roomId)
	{
		return 'room:' . (int)$roomId;
	}

	function getUserImageId($userId)
	{
		$userId = (int)$userId;
		if (!$userId)
			return 0;

		try {
			return (int)GW::db()->fetch_result("SELECT id FROM gw_images WHERE owner='GW_User_{$userId}_image' ORDER BY id DESC LIMIT 1");
		} catch (Throwable $e) {
			return 0;
		}
	}

	function getRecipientUnreadMessageCount($recipientId, $roomId)
	{
		$recipientId = (int)$recipientId;
		$roomId = (int)$roomId;
		if (!$recipientId || !$roomId)
			return 0;

		try {
			$lastSeen = (int)GW::db()->fetch_result("SELECT last_seen_message_id FROM gw_chat_room_users WHERE room_id={$roomId} AND user_id={$recipientId} LIMIT 1");
			return (int)GW::db()->fetch_result("
				SELECT COUNT(*)
				FROM gw_chat_messages
				WHERE room_id={$roomId}
				  AND sender_id!={$recipientId}
				  AND is_deleted=0
				  AND id>{$lastSeen}
			");
		} catch (Throwable $e) {
			return 0;
		}
	}

	function parseImportantPushMessage($message)
	{
		$message = trim((string)$message);
		if (!preg_match('/^!important\b[\s:;-]*/i', $message, $match))
			return [false, $message];

		$pushMessage = trim(substr($message, strlen($match[0])));
		return [true, $pushMessage !== '' ? $pushMessage : $message];
	}

	function getPrivatePushCooldownRemaining($recipientId, $roomId)
	{
		$cooldown = max(0, $this->chatConfigInt('push_private_room_cooldown_seconds', 180));
		if (!$cooldown)
			return 0;

		$lastSent = GW_Temp_Data::singleton()->readValue((int)$recipientId, self::PUSH_SENT_GROUP, $this->pushCooldownName($roomId));
		if (!$lastSent)
			return 0;

		$remaining = strtotime((string)$lastSent) + $cooldown - time();
		return max(0, (int)$remaining);
	}

	function canQueuePrivatePush($recipient, $roomId, $force = false, $checkCooldown = true)
	{
		if (!$recipient || !$recipient->id)
			return false;

		if ($force)
			return true;

		$offlineAfter = max(1, $this->chatConfigInt('push_private_offline_after_seconds', 90));
		$lastRequestTs = $recipient->last_request_time ? strtotime($recipient->last_request_time) : 0;
		if ($lastRequestTs && $lastRequestTs > time() - $offlineAfter)
			return false;

		if ($this->isUserWsOnline((int)$recipient->id))
			return false;

		if ($checkCooldown && $this->getPrivatePushCooldownRemaining((int)$recipient->id, $roomId))
			return false;

		return true;
	}

	function queuePrivatePushNotifications($room, $sender, array $packet)
	{
		if (!$room || (string)$room->type !== 'private' || !$sender)
			return;

		list($important, $pushMessage) = $this->parseImportantPushMessage($packet['message'] ?? '');

		if (!$important && !$this->chatConfigBool('push_private_enabled', false))
			return;

		if (!$important && $this->isQuietHoursNow())
			return;

		$memberIds = $this->getRoomUserIds($room->id, true);
		$users = $this->getUsersIndexed($memberIds);
		$temp = GW_Temp_Data::singleton();
		$queued = 0;

		foreach ($memberIds as $recipientId) {
			$recipientId = (int)$recipientId;
			if (!$recipientId || $recipientId === (int)$sender->id)
				continue;

			$recipient = $users[$recipientId] ?? null;
			if (!$this->canQueuePrivatePush($recipient, (int)$room->id, $important, false))
				continue;

			$name = $important ? ($this->pushQueueName((int)$room->id) . ':important:' . (int)($packet['message_id'] ?? 0)) : $this->pushQueueName((int)$room->id);
			$existing = $temp->readValue($recipientId, self::PUSH_QUEUE_GROUP, $name);
			$existingData = $existing ? json_decode((string)$existing, true) : [];
			$count = $important ? 1 : max(((int)($existingData['count'] ?? 0) + 1), $this->getRecipientUnreadMessageCount($recipientId, (int)$room->id), 1);

			$payload = [
				'recipient_user_id' => $recipientId,
				'room_id' => (int)$room->id,
				'message_id' => (int)($packet['message_id'] ?? 0),
				'sender_id' => (int)$sender->id,
				'sender_name' => $this->getUserDisplayName($sender),
				'sender_image_id' => $this->getUserImageId((int)$sender->id),
				'message' => $pushMessage,
				'count' => $count,
				'important' => $important ? 1 : 0,
				'insert_time' => $this->now(),
				'last_update_time' => $this->now(),
			];

			$temp->store($recipientId, self::PUSH_QUEUE_GROUP, $name, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '1 hour');
			$queued++;
		}

		if (!$queued)
			return;

		try {
			GW_Task::singleton()->addSingle('chat_push_queue');
		} catch (Throwable $e) {
			error_log('GW_Chat_Service chat_push_queue task add failed: '.$e->getMessage());
		}
	}

	function getPushBaseUrl()
	{
		$base = rtrim((string)GW::s('SITE_URL'), '/');
		if ($base)
			return $base;

		$host = trim((string)GW::s('MAIN_HOST'));
		return $host ? ('https://' . $host) : '';
	}

	function getPrivatePushIcon(array $job)
	{
		$base = $this->getPushBaseUrl();
		if (!$base)
			return '';

		$senderImageId = (int)($job['sender_image_id'] ?? 0);
		if ($senderImageId)
			return $base . '/tools/imga/' . $senderImageId . '?size=128x128&method=crop';

		try {
			$favicoId = (int)GW::db()->fetch_result("SELECT id FROM gw_images WHERE owner='GW_Site_1_favico' ORDER BY id DESC LIMIT 1");
			if ($favicoId)
				return $base . '/tools/imga/' . $favicoId . '?size=128x128&method=crop';
		} catch (Throwable $e) {}

		return '';
	}

	function buildPrivatePushPayload(array $job)
	{
		$senderName = trim((string)($job['sender_name'] ?? ''));
		$count = max(1, (int)($job['count'] ?? 1));
		$message = trim((string)($job['message'] ?? ''));
		$previewEnabled = $this->chatConfigBool('push_private_preview_enabled', true);
		$previewMax = max(20, $this->chatConfigInt('push_private_preview_max_length', 120));
		$title = $senderName ? ('New message from ' . $senderName) : 'New private chat message';
		$body = $count > 1 ? ($count . ' new messages') : 'New message';

		if ($previewEnabled && $message !== '') {
			if (function_exists('mb_strlen') && mb_strlen($message, 'UTF-8') > $previewMax)
				$message = mb_substr($message, 0, $previewMax - 1, 'UTF-8') . '...';
			elseif (strlen($message) > $previewMax)
				$message = substr($message, 0, $previewMax - 1) . '...';

			$body = $count > 1 ? ($count . ' new messages: ' . $message) : $message;
		}

		$ln = strtolower(GW::$context->app->ln ?? 'lt');
		$base = $this->getPushBaseUrl();
		$url = $base . '/admin/' . $ln . '/users/chat/room?id=' . (int)($job['room_id'] ?? 0) . '&room_type=private';

		$payload = [
			'title' => $title,
			'body' => $body,
			'tag' => 'gw-chat-private-' . (int)($job['room_id'] ?? 0),
			'data' => ['url' => $url],
		];

		$icon = $this->getPrivatePushIcon($job);
		if ($icon)
			$payload['icon'] = $icon;

		return $payload;
	}

	function processPrivatePushQueue($limit = 50)
	{
		$list = GW_Temp_Data::singleton()->findAll("`group`='".GW::db()->escape(self::PUSH_QUEUE_GROUP)."' AND expires > '".date('Y-m-d H:i:s')."'", ['limit' => max(1, (int)$limit)]);
		$sent = 0;

		foreach ($list as $item) {
			$job = json_decode((string)$item->value, true);
			if (!is_array($job) || empty($job['recipient_user_id']) || empty($job['room_id'])) {
				$item->delete();
				continue;
			}

			$recipientId = (int)$job['recipient_user_id'];
			$recipient = GW_User::singleton()->find(['id=? AND active=1 AND removed=0', $recipientId]);

			$important = !empty($job['important']);
			if ((!$important && !$this->chatConfigBool('push_private_enabled', false)) || !$this->canQueuePrivatePush($recipient, (int)$job['room_id'], $important, false)) {
				$item->delete();
				continue;
			}

			if (!$important && ($this->isQuietHoursNow() || $this->getPrivatePushCooldownRemaining($recipientId, (int)$job['room_id'])))
				continue;

			try {
				$result = GW_Android_Push_Notif::pushWeb($recipientId, $this->buildPrivatePushPayload($job));
			} catch (Throwable $e) {
				error_log('GW_Chat_Service private push failed for user '.$recipientId.': '.$e->getMessage());
				$item->delete();
				continue;
			}
			if (!$important)
				GW_Temp_Data::singleton()->store($recipientId, self::PUSH_SENT_GROUP, $this->pushCooldownName((int)$job['room_id']), date('Y-m-d H:i:s'), '30 day');
			$item->delete();
			$success = false;
			foreach ((array)$result as $report) {
				if (!empty($report['success'])) {
					$success = true;
					break;
				}
			}
			if ($success)
				$sent++;
		}

		return ['processed' => count($list), 'sent' => $sent];
	}

	function getChatFileConfig($key, $default = null)
	{
		$key = strtolower((string)$key);
		$gwKey = 'CHAT_FILES/' . strtoupper($key);
		$value = GW::s($gwKey);

		if ($value !== null && $value !== false && $value !== '')
			return $value;

		foreach (['users__chat/' . $key, 'chat_files/' . $key] as $cfgKey) {
			try {
				$value = GW_Config::singleton()->get($cfgKey);
				if ($value !== null && $value !== false && $value !== '')
					return $value;
			} catch (Throwable $e) {}
		}

		if ($key === 'allowed_extensions') {
			foreach (['users__chat/allowed_extenions', 'chat_files/allowed_extenions'] as $cfgKey) {
				try {
					$value = GW_Config::singleton()->get($cfgKey);
					if ($value !== null && $value !== false && $value !== '')
						return $value;
				} catch (Throwable $e) {}
			}
		}

		if ($key === 'allowed_attachment_size') {
			try {
				$value = GW_Config::singleton()->get('chat_files/max_upload_size');
				if ($value !== null && $value !== false && $value !== '')
					return $value;
			} catch (Throwable $e) {}
		}

		return $default;
	}

	function getRemoteStoreConfig($key, $default = null)
	{
		$key = strtolower((string)$key);
		$gwKey = 'CHAT_FILES/' . strtoupper($key);
		$value = GW::s($gwKey);

		if ($value !== null && $value !== false && $value !== '')
			return $value;

		foreach (['users__chat/' . $key, 'chat_files/' . $key] as $cfgKey) {
			try {
				$value = GW_Config::singleton()->get($cfgKey);
				if ($value !== null && $value !== false && $value !== '')
					return $value;
			} catch (Throwable $e) {}
		}

		return $default;
	}

	function getAttachmentStorageMode()
	{
		$mode = strtolower(trim((string)$this->getChatFileConfig('attachment_storage', self::ATTACHMENT_STORAGE_LOCAL)));

		if (in_array($mode, ['remote', 'mirror', '1voro', '1.voro.lt'], true))
			$mode = self::ATTACHMENT_STORAGE_VORO1;

		if (!in_array($mode, [self::ATTACHMENT_STORAGE_LOCAL, self::ATTACHMENT_STORAGE_VORO1], true))
			$mode = self::ATTACHMENT_STORAGE_LOCAL;

		return $mode;
	}

	function useRemoteAttachmentStorage()
	{
		return $this->getAttachmentStorageMode() === self::ATTACHMENT_STORAGE_VORO1;
	}

	function getAllowedAttachmentExtensions()
	{
		$raw = (string)$this->getChatFileConfig('allowed_extensions', self::DEFAULT_ALLOWED_ATTACHMENT_EXTENSIONS);
		$parts = preg_split('/[\s,;]+/', strtolower($raw), -1, PREG_SPLIT_NO_EMPTY);
		$out = [];

		foreach ($parts as $ext) {
			$ext = trim($ext, " .\t\n\r\0\x0B");
			if ($ext && preg_match('/^[a-z0-9]+$/', $ext))
				$out[$ext] = 1;
		}

		if (!$out)
			$out = array_fill_keys(explode(',', self::DEFAULT_ALLOWED_ATTACHMENT_EXTENSIONS), 1);

		return array_keys($out);
	}

	function getAllowedAttachmentSizeBytes()
	{
		$value = trim((string)$this->getChatFileConfig('allowed_attachment_size', self::DEFAULT_ATTACHMENT_SIZE_MB));

		if ($value === '')
			return self::DEFAULT_ATTACHMENT_SIZE_MB * 1024 * 1024;

		if (preg_match('/^(\d+(?:\.\d+)?)\s*(b|kb|mb|gb|k|m|g)?$/i', $value, $m)) {
			$number = (float)$m[1];
			$unit = strtolower($m[2] ?? '');

			if ($unit === 'b')
				return (int)$number;
			if ($unit === 'kb' || $unit === 'k')
				return (int)round($number * 1024);
			if ($unit === 'gb' || $unit === 'g')
				return (int)round($number * 1024 * 1024 * 1024);
			if ($unit === '')
				return $number > 1024 ? (int)$number : (int)round($number * 1024 * 1024);

			return (int)round($number * 1024 * 1024);
		}

		return self::DEFAULT_ATTACHMENT_SIZE_MB * 1024 * 1024;
	}

	function getMaxFilesPerMessage()
	{
		return max(1, (int)$this->getChatFileConfig('max_files_per_message', 5));
	}

	function getChatFilesBaseUrl()
	{
		$base = (string)$this->getRemoteStoreConfig('public_base_url', '');
		if (!$base)
			$base = (string)GW::s('SITE_URL');

		if (!$base) {
			$host = $_SERVER['HTTP_HOST'] ?? '';
			$base = $host ? 'https://' . $host . '/' : '/';
		}

		return rtrim($base, '/') . '/';
	}

	function getDefaultRemoteStoreEndpoint()
	{
		$host = (string)($_SERVER['HTTP_HOST'] ?? '');
		$host = preg_replace('/\.localhost$/', '', $host);

		if (!$host || strpos($host, '.1.voro.lt') !== false)
			return '';

		return 'https://' . str_replace('.', '-', $host) . '.1.voro.lt/tools/chat_store';
	}

	function normalizeUploadFiles($files)
	{
		if (!$files || empty($files['name']))
			return [];

		if (!is_array($files['name']))
			return [$files];

		$out = [];
		foreach ($files['name'] as $idx => $name) {
			$out[] = [
				'name' => $name,
				'type' => $files['type'][$idx] ?? '',
				'tmp_name' => $files['tmp_name'][$idx] ?? '',
				'error' => $files['error'][$idx] ?? UPLOAD_ERR_NO_FILE,
				'size' => $files['size'][$idx] ?? 0,
			];
		}

		return $out;
	}

	function validateUploadFile($file)
	{
		if (!$file || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)
			return false;

		if ((int)$file['error'] !== UPLOAD_ERR_OK)
			throw new Exception('File upload failed');

		$maxSize = $this->getAllowedAttachmentSizeBytes();
		if ((int)$file['size'] <= 0)
			throw new Exception('Empty file');
		if ((int)$file['size'] > $maxSize)
			throw new Exception('File is too large. Max: ' . GW_Math_Helper::cFileSize($maxSize));

		$ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
		$blocked = ['php','phtml','phar','cgi','pl','asp','aspx','jsp','js','mjs','html','htm','shtml','svg'];
		if (!$ext || in_array($ext, $blocked, true))
			throw new Exception('File type is not allowed');
		if (!in_array($ext, $this->getAllowedAttachmentExtensions(), true))
			throw new Exception('File extension is not allowed');

		return true;
	}

	function detectMime($filename)
	{
		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if ($finfo) {
				$mime = finfo_file($finfo, $filename);
				finfo_close($finfo);
				if ($mime)
					return $mime;
			}
		}

		return Mime_Type_Helper::getByFilename($filename) ?: 'application/octet-stream';
	}

	function sanitizeOriginalFilename($filename)
	{
		$filename = trim((string)$filename);
		$filename = basename(str_replace('\\', '/', $filename));
		$filename = preg_replace('/[^\pL\pN._ -]+/u', '_', $filename);
		$filename = trim($filename, " .\t\n\r\0\x0B");

		return $filename ?: 'file';
	}

	function randomStorageKey($len = 32)
	{
		$raw = bin2hex(random_bytes(24));
		return substr($raw, 0, max(25, (int)$len));
	}

	function isImageMime($mime)
	{
		return in_array(strtolower((string)$mime), ['image/jpeg','image/png','image/gif','image/webp','image/bmp','image/tiff'], true);
	}

	function attachmentBaseUrlFromStoredUrl($url)
	{
		$url = (string)$url;
		if ($url === '')
			return '';

		foreach (['/tools/', '/repository/'] as $marker) {
			$pos = strpos($url, $marker);
			if ($pos !== false)
				return substr($url, 0, $pos + 1);
		}

		return '';
	}

	function buildAttachmentUrls($relpath, $kind, $base = null, $downloadName = '')
	{
		$base = $base === null ? $this->getChatFilesBaseUrl() : (string)$base;
		$base = rtrim($base, '/') . '/';
		$encodedRelpath = rawurlencode($relpath);
		$publicUrl = $base . 'repository/' . str_replace('%2F', '/', $encodedRelpath);
		$thumbUrl = '';

		if ($kind === 'image') {
			$chatFilesPrefix = '.sys/chat_files/';
			$imgFile = strpos($relpath, $chatFilesPrefix) === 0 ? substr($relpath, strlen($chatFilesPrefix)) : $relpath;
			$imgDirId = strpos($relpath, $chatFilesPrefix) === 0 ? 'chatfiles' : 'repository';
			$publicUrl = $base . 'tools/img_resize?file=' . rawurlencode($imgFile) . '&dirid=' . $imgDirId;
			$thumbUrl = $publicUrl . '&size=720x720';
		} elseif (strpos($relpath, '.sys/chat_files/') === 0) {
			$file = substr($relpath, strlen('.sys/chat_files/'));
			$publicUrl = $base . 'tools/download?file=' . rawurlencode($file) . '&dirid=chatfiles';

			if ($downloadName !== '')
				$publicUrl .= '&name=' . rawurlencode($downloadName);
		}

		return [$publicUrl, $thumbUrl];
	}

	function storeChatAttachmentLocal($roomId, $userId, $sourceFile, $originalFilename, $mime = '')
	{
		$roomId = (int)$roomId;
		$userId = (int)$userId;
		$originalFilename = $this->sanitizeOriginalFilename($originalFilename);
		$ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
		$mime = $mime ?: $this->detectMime($sourceFile);
		$kind = $this->isImageMime($mime) ? 'image' : 'file';
		$storedFilename = $this->randomStorageKey() . ($ext ? '.' . $ext : '');
		$relDir = '.sys/chat_files/' . $roomId;
		$relpath = $relDir . '/' . $storedFilename;
		$targetDir = GW::s('DIR/REPOSITORY') . $relDir . '/';

		if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true))
			throw new Exception('Failed to create chat upload directory');

		$target = $targetDir . $storedFilename;
		if (is_uploaded_file($sourceFile)) {
			if (!move_uploaded_file($sourceFile, $target))
				throw new Exception('Failed to store uploaded file');
		} else {
			if (!copy($sourceFile, $target))
				throw new Exception('Failed to store file');
		}

		@chmod($target, 0664);
		[$publicUrl, $thumbUrl] = $this->buildAttachmentUrls($relpath, $kind, null, $originalFilename);

		return [
			'storage' => 'local',
			'kind' => $kind,
			'original_filename' => $originalFilename,
			'stored_filename' => $storedFilename,
			'relpath' => $relpath,
			'mime' => $mime,
			'size' => filesize($target) ?: 0,
			'public_url' => $publicUrl,
			'thumb_url' => $thumbUrl,
		];
	}

	function postRemoteAttachment($roomId, $userId, $file)
	{
		if (!$this->useRemoteAttachmentStorage())
			return null;

		$endpoint = (string)$this->getRemoteStoreConfig('remote_store_endpoint', '');
		$token = (string)$this->getRemoteStoreConfig('remote_store_token', '');

		if (!$endpoint && $token)
			$endpoint = $this->getDefaultRemoteStoreEndpoint();

		if (!$endpoint)
			return null;
		if (!$token)
			throw new Exception('Remote chat storage token is missing');

		$post = [
			'token' => $token,
			'room_id' => (int)$roomId,
			'user_id' => (int)$userId,
			'filename' => $this->sanitizeOriginalFilename($file['name']),
			'mime' => $this->detectMime($file['tmp_name']),
			'filedata' => base64_encode(file_get_contents($file['tmp_name'])),
		];

		$ch = curl_init($endpoint);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query($post),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 60,
		]);
		$raw = curl_exec($ch);
		$error = curl_error($ch);
		$status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($raw === false || $status < 200 || $status >= 300)
			throw new Exception('Remote chat storage failed' . ($error ? ': ' . $error : ''));

		$resp = json_decode($raw, true);
		if (!is_array($resp) || empty($resp['ok']) || empty($resp['file']))
			throw new Exception('Remote chat storage returned invalid response');

		$meta = $resp['file'];
		$meta['storage'] = 'remote';
		return $meta;
	}

	function prepareUploadedAttachments($roomId, $userId, $files)
	{
		$this->ensureRoomAccess($roomId, $userId, true);
		$files = $this->normalizeUploadFiles($files);
		$maxFiles = $this->getMaxFilesPerMessage();
		$out = [];

		foreach ($files as $file) {
			if (!$this->validateUploadFile($file))
				continue;
			if (count($out) >= $maxFiles)
				throw new Exception('Too many files');

			$remote = $this->postRemoteAttachment($roomId, $userId, $file);
			$out[] = $remote ?: $this->storeChatAttachmentLocal($roomId, $userId, $file['tmp_name'], $file['name'], $this->detectMime($file['tmp_name']));
		}

		return $out;
	}

	function storeAttachmentRows($messageId, $roomId, $userId, $attachments, $time)
	{
		$out = [];

		foreach ((array)$attachments as $meta) {
			$item = GW_Chat_Attachment::singleton()->createNewObject([
				'message_id' => (int)$messageId,
				'room_id' => (int)$roomId,
				'uploader_id' => (int)$userId,
				'storage' => $meta['storage'] ?? 'local',
				'kind' => $meta['kind'] ?? 'file',
				'original_filename' => $meta['original_filename'] ?? '',
				'stored_filename' => $meta['stored_filename'] ?? '',
				'relpath' => $meta['relpath'] ?? '',
				'mime' => $meta['mime'] ?? '',
				'size' => (int)($meta['size'] ?? 0),
				'public_url' => $meta['public_url'] ?? '',
				'thumb_url' => $meta['thumb_url'] ?? '',
				'is_deleted' => 0,
				'insert_time' => $time,
				'update_time' => $time,
			]);
			$item->insert();
			$out[] = $this->attachmentRowToArray($item->toArray());
		}

		return $out;
	}

	function sendMessage($roomId, $senderId, $message, $opts = [])
	{
		$room = $this->getRoom($roomId);

		if (!$room || !$room->is_active)
			throw new Exception('Room not found');

		$this->ensureRoomAccess($roomId, $senderId, true);

		$message = trim((string)$message);
		$attachments = $opts['attachments'] ?? [];
		if ($message === '' && !$attachments)
			throw new Exception('Empty message');

		$sender = $this->ensureUserExists($senderId);
		$time = $this->now();
		$prevRoomMessageId = (int)($room->last_message_id ?? 0);

		$item = GW_Chat_Message::singleton()->createNewObject([
			'room_id' => (int)$roomId,
			'sender_id' => (int)$senderId,
			'message' => $message,
			'source' => $opts['source'] ?? 'web',
			'mentions_json' => isset($opts['mentions']) ? json_encode($opts['mentions']) : null,
			'reply_to_message_id' => (int)($opts['reply_to_message_id'] ?? 0),
			'is_deleted' => 0,
			'insert_time' => $time,
			'update_time' => $time
		]);

		$item->insert();
		$attachmentRows = $this->storeAttachmentRows($item->id, $roomId, $senderId, $attachments, $time);
		$event = $this->createRoomEvent($roomId, $senderId, 'message', $item->id, [
			'source' => $item->source,
		], $time);
		$this->touchRoomActivity($room, $time, $event->id, $item->id, $time);

		$this->trimRoomHistoryIfNeeded($room);

		$packet = [
			'action' => 'chat_message',
			'room_id' => (int)$roomId,
			'room_type' => $room->type,
			'message_id' => (int)$item->id,
			'prev_room_message_id' => $prevRoomMessageId,
			'sender_id' => (int)$senderId,
			'sender_username' => $sender->username,
			'sender_name' => $this->getUserDisplayName($sender),
			'message' => $message,
			'source' => $item->source,
			'reply_to_message_id' => (int)$item->reply_to_message_id,
			'reactions' => [],
			'attachments' => $attachmentRows,
			'insert_time' => $time
		];

		$this->notifyRoom($roomId, $packet);
		$this->queuePrivatePushNotifications($room, $sender, $packet);

		return $packet;
	}

	function toggleMessageReaction($messageId, $userId, $reaction)
	{
		$messageId = (int)$messageId;
		$userId = (int)$userId;
		$reaction = $this->normalizeReaction($reaction);
		$message = $this->getMessageById($messageId);

		if (!$message || (int)$message->is_deleted)
			throw new Exception('Message not found');

		$roomId = (int)$message->room_id;
		$this->ensureRoomAccess($roomId, $userId, true);
		$time = $this->now();

		$item = GW_Chat_Message_Reaction::singleton()->find([
			'message_id=? AND user_id=?',
			$messageId,
			$userId
		]);

		$currentReaction = $item ? (string)$item->reaction : '';
		$previousReaction = '';

		if ($item && $currentReaction === $reaction) {
			$item->delete();
			$eventType = 'reaction_remove';
			$currentReaction = '';
		} elseif ($item) {
			$previousReaction = $currentReaction;
			$item->saveValues([
				'reaction' => $reaction,
				'update_time' => $time,
			]);
			$eventType = 'reaction_change';
			$currentReaction = $reaction;
		} else {
			$item = GW_Chat_Message_Reaction::singleton()->createNewObject([
				'message_id' => $messageId,
				'room_id' => $roomId,
				'user_id' => $userId,
				'reaction' => $reaction,
				'insert_time' => $time,
				'update_time' => $time,
			]);
			$item->insert();
			$eventType = 'reaction_add';
			$currentReaction = $reaction;
		}

		$user = $this->ensureUserExists($userId);
		$eventPayload = $this->buildUserEventPayload($user, [
			'message_id' => $messageId,
			'reaction' => $currentReaction ?: $reaction,
		]);

		if (!empty($previousReaction))
			$eventPayload['previous_reaction'] = $previousReaction;

		$event = $this->createRoomEvent($roomId, $userId, $eventType, $messageId, $eventPayload, $time);
		$this->touchRoomActivity($this->getRoom($roomId), $time, $event->id);

		return [
			'action' => 'chat_reaction_update',
			'room_id' => $roomId,
			'message_id' => $messageId,
			'user_id' => $userId,
			'event_id' => (int)$event->id,
			'event_type' => $eventType,
			'event_text' => $this->eventToTimelineEntry($event->toArray())['text'],
			'reaction' => $currentReaction,
			'reactions' => $this->getMessageReactions($messageId, $userId),
			'insert_time' => $time,
		];
	}

	function markSeen($roomId, $userId, $lastMessageId)
	{
		$room = $this->getRoom($roomId);
		$membership = $this->ensureRoomAccess($roomId, $userId, true);
		$time = $this->now();
		$lastMessageId = (int)$lastMessageId;
		$lastEventId = $room ? (int)$room->last_event_id : 0;
		$currentSeenMessageId = (int)($membership->last_seen_message_id ?? 0);
		$currentSeenEventId = (int)($membership->last_seen_event_id ?? 0);

		$packet = [
			'action' => 'chat_seen',
			'room_id' => (int)$roomId,
			'user_id' => (int)$userId,
			'last_message_id' => $lastMessageId,
			'last_event_id' => $lastEventId,
			'insert_time' => $time
		];

		if ($lastMessageId <= $currentSeenMessageId && $lastEventId <= $currentSeenEventId) {
			$packet['_no_broadcast'] = 1;
			return $packet;
		}

		$membership->saveValues([
			'last_seen_message_id' => max($lastMessageId, $currentSeenMessageId),
			'last_seen_event_id' => max($lastEventId, $currentSeenEventId),
			'last_seen_time' => $time,
			'update_time' => $time
		]);

		$this->notifyRoom($roomId, $packet, $userId);

		return $packet;
	}

	function typing($roomId, $userId, $typing = true)
	{
		$this->ensureRoomAccess($roomId, $userId, true);
		$user = $this->ensureUserExists($userId);

		$packet = [
			'action' => $typing ? 'chat_typing' : 'chat_stop_typing',
			'room_id' => (int)$roomId,
			'user_id' => (int)$userId,
			'user_name' => $this->getUserDisplayName($user),
			'insert_time' => $this->now()
		];

		$this->notifyRoom($roomId, $packet, $userId);

		return $packet;
	}
}
