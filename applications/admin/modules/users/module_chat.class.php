<?php

class Module_Chat extends GW_Common_Module
{
	public $default_view = 'list';
	protected $roomListMeta = [];
	protected $pendingMemberIds = [];
	protected $reactFullStatusCache = null;

	function init()
	{
		$act = $_REQUEST['act'] ?? '';
		if (!$this->app->user && $act !== 'doStoreChatAttachmentFile')
			die('no access');

		$this->model = GW_Chat_Room::singleton();
		if (!$this->app->user && $act === 'doStoreChatAttachmentFile')
			return;

		parent::init();
		$this->list_params['paging_enabled'] = 1;
		$this->list_params['order'] = 'COALESCE(last_event_time, last_message_time, update_time) DESC, id DESC';
		$this->options['room_type'] = ['group' => 'group', 'private' => 'private'];
	}

	function jsonResponse($data, $status = 200)
	{
		http_response_code($status);
		header('Content-type: application/json');
		echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		exit;
	}

	function jsonError($message, $status = 400, $extra = [])
	{
		$this->jsonResponse(['ok' => 0, 'error' => $message] + $extra, $status);
	}

	function svc()
	{
		return GW_Chat_Service::singleton();
	}

	function isReactServiceEnabled()
	{
		return GW_WebSocket_Helper2::enabled();
	}

	function currentUser()
	{
		return $this->app->user ?: null;
	}

	function chatDebugEnabled()
	{
		return !empty($_REQUEST['gwchat_debug'])
			|| (int)GW_WebSocket_Helper2::chatConfigValue('full_chat_debug')
			|| (int)GW_WebSocket_Helper2::chatConfigValue('wss_log_to_console');
	}

	function chatConsoleDebugEnabled()
	{
		return (int)GW_WebSocket_Helper2::chatConfigValue('full_chat_debug')
			|| (int)GW_WebSocket_Helper2::chatConfigValue('wss_log_to_console');
	}

	function userAvatarUrl($user, $size = '40x40')
	{
		
		
		if (!$user || !$user->image)
			return '';
	

		return $this->app->sys_base . 'tools/imga/' . $user->image->id . '?size=' . rawurlencode($size) . '&method=crop';
	}

	function userToChatArray($user)
	{
		if (!$user)
			return null;

		$title = trim(($user->name ?? '') . ' ' . ($user->surname ?? ''));

		return [
			'id' => (int)$user->id,
			'username' => (string)$user->username,
			'name' => $title ?: (string)$user->username,
			'image_url' => $this->userAvatarUrl($user),
			'last_request_time' => (string)$user->last_request_time,
			'last_request_ts' => $user->last_request_time ? (int)strtotime($user->last_request_time) : 0,
			'last_request_ago' => $user->last_request_time ? GW_Math_Helper::uptime(max(0, time() - strtotime($user->last_request_time)), 1) : '',
		];
	}

	function canShowSidebarLastRequestUri()
	{
		return $this->app->user
			&& (int)$this->app->user->id === 9
			&& (int)GW_Config::singleton()->get('gw_users/onlinechat_show_last_request_uri');
	}

	function addSidebarDebugInfo(&$data, $user)
	{
		if (!$this->canShowSidebarLastRequestUri() || !$user)
			return;

		$data['last_request_uri'] = (string)$user->get('keyval/last_request_uri');
	}

	function getSafeReturnUri($fallback = '')
	{
		$returnTo = (string)($_REQUEST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? ''));

		if ($returnTo !== '' && strpos($returnTo, '/') === 0 && strpos($returnTo, '//') !== 0)
			return $returnTo;

		return $fallback ?: $this->app->buildUri($this->module_path_clean);
	}

	function roomLink($roomId, $params = [])
	{
		return $this->app->buildUri($this->module_path_clean . '/room', ['id' => (int)$roomId] + (array)$params);
	}

	function getRoomListMeta($roomId)
	{
		$roomId = (int)$roomId;

		if (!$roomId)
			return [];

		if (!isset($this->roomListMeta[$roomId])) {
			$room = $this->svc()->getRoom($roomId);
			$this->roomListMeta[$roomId] = $room ? $this->svc()->roomToArray($room, (int)$this->app->user->id) : [];

			if (!empty($this->roomListMeta[$roomId]['members']))
				foreach ($this->roomListMeta[$roomId]['members'] as &$member)
					$member = (object)$member;
		}

		return $this->roomListMeta[$roomId];
	}

	function getRoomDisplayTitle($room)
	{
		$meta = $this->getRoomListMeta($room->id);
		return trim((string)($meta['display_title'] ?? $room->title ?: ('Room #' . $room->id)));
	}

	function __eventBeforeListParams(&$params)
	{
		$userId = (int)$this->app->user->id;
		$cond = "a.id IN (SELECT room_id FROM gw_chat_room_users WHERE user_id={$userId} AND is_active=1)";
		$params['conditions'] = empty($params['conditions']) ? $cond : '(' . $params['conditions'] . ') AND ' . $cond;
	}

	function __eventAfterList(&$list)
	{
		foreach ($list as $item)
			$this->getRoomListMeta($item->id);
	}

	function canBeAccessed($item, $opts=[])
	{
		if (!$item || !method_exists($item, 'load_if_not_loaded'))
			return true;

		$item->load_if_not_loaded();
		$result = $this->app->user->isRoot() || (bool)$this->svc()->getMembership((int)$item->id, (int)$this->app->user->id, false);

		if (!isset($opts['die']) || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		$this->jump($this->app->page->path);
	}

	function getListConfig()
	{
		$cfg = parent::getListConfig();

		$cfg['fields']['id'] = 'Lof';
		$cfg['fields']['title'] = 'Lof';
		$cfg['fields']['type'] = 'Lof';
		$cfg['fields']['member_usernames'] = 'Lof';
		$cfg['fields']['insert_time'] = 'lof';
		$cfg['fields']['last_message_time'] = 'lof';
		$cfg['fields']['last_event_time'] = 'lof';

		$cfg['filters']['id'] = 1;
		$cfg['filters']['title'] = 1;
		$cfg['filters']['type'] = 1;
		$cfg['filters']['insert_time'] = 1;
		$cfg['filters']['last_message_time'] = 1;
		$cfg['filters']['last_event_time'] = 1;

		
		return $cfg;
	}

	function viewForm()
	{
		$item = $this->getDataObjectById();

		if (!$item)
			$item = $this->model->createNewObject();

		if (!$item->id) {
			$item->type = $item->type ?: 'group';
			$item->is_active = isset($item->is_active) ? $item->is_active : 1;
			$item->room_history_limit = $item->room_history_limit ?: 1000;
			$item->creator_id = $item->creator_id ?: (int)$this->app->user->id;
		}

		$item->member_ids = $this->svc()->getRoomUserIds((int)$item->id, true);
		if (!$item->member_ids)
			$item->member_ids = [(int)$this->app->user->id];

		$this->tpl_vars['item'] = $item;
		$this->tpl_vars['is_root_chat_admin'] = $this->app->user->isRoot() ? 1 : 0;
	}

	function __eventBeforeSave($item)
	{
		$this->pendingMemberIds = $this->parseUserIds($_REQUEST['item']['member_ids'] ?? []);

		if (!$this->pendingMemberIds)
			$this->pendingMemberIds = [(int)$this->app->user->id];

		if (!$this->app->user->isRoot()) {
			$this->pendingMemberIds[] = (int)$this->app->user->id;
			$this->pendingMemberIds = array_values(array_unique(array_filter(array_map('intval', $this->pendingMemberIds))));

			if ($item->id) {
				$existing = $this->model->find(['id=?', (int)$item->id]);
				if ($existing) {
					foreach (['type','direct_key','creator_id','is_active','room_history_limit','last_message_id','last_message_time','last_event_id','last_event_time','insert_time'] as $field)
						$item->$field = $existing->$field;
				}
			} else {
				$item->type = 'group';
				$item->direct_key = null;
				$item->creator_id = (int)$this->app->user->id;
				$item->is_active = 1;
				$item->room_history_limit = (int)$item->room_history_limit ?: 1000;
				$item->last_message_id = 0;
				$item->last_message_time = null;
				$item->last_event_id = 0;
				$item->last_event_time = null;
			}
		} else {
			$item->room_history_limit = (int)$item->room_history_limit ?: null;
			if (($item->type ?? '') !== 'private')
				$item->direct_key = null;
		}

		unset($item->member_ids);
	}

	function __eventAfterSave($item)
	{
		$roomId = (int)$item->id;
		if (!$roomId)
			return;

		$creatorId = (int)($item->creator_id ?: $this->app->user->id);
		$selectedIds = $this->pendingMemberIds ?: $this->svc()->getRoomUserIds($roomId, true);

		if (!$selectedIds)
			$selectedIds = [$creatorId ?: (int)$this->app->user->id];

		$selectedIds = array_values(array_unique(array_filter(array_map('intval', $selectedIds))));
		$memberships = $this->svc()->getRoomUsers($roomId, false);
		$existingByUserId = [];

		foreach ($memberships as $membership)
			$existingByUserId[(int)$membership->user_id] = $membership;

		foreach ($selectedIds as $userId) {
			$role = $existingByUserId[$userId]->role ?? ($userId === $creatorId ? 'owner' : 'member');
			$this->svc()->ensureMembership($roomId, $userId, $role);
		}

		foreach ($existingByUserId as $userId => $membership) {
			if (in_array($userId, $selectedIds))
				continue;

			$membership->saveValues([
				'is_active' => 0,
				'update_time' => date('Y-m-d H:i:s'),
			]);
		}

		$this->pendingMemberIds = [];
	}

	function getRoomsForBubble()
	{
		$rooms = $this->svc()->getMyRooms((int)$this->app->user->id);
		$ackTime = (string)$this->currentUser()->get('keyval/chatbubble_aknowledged_at');
		$ackTs = $ackTime ? strtotime($ackTime) : 0;

		foreach ($rooms as &$room) {
			$room['room_url'] = $this->roomLink($room['id']);
			$room['display_user'] = null;
			$lastTs = !empty($room['last_event_time']) ? strtotime($room['last_event_time']) : (!empty($room['last_message_time']) ? strtotime($room['last_message_time']) : 0);
			$room['bubble_has_unread_activity'] = !empty($room['unread_activity_count']) ? 1 : 0;
			$room['bubble_unread_count'] = (int)($room['unread_count'] ?? 0) + ($room['bubble_has_unread_activity'] ? 1 : 0);

			if (($room['type'] ?? '') === 'private' && !empty($room['display_user_id'])) {
				$user = GW_User::singleton()->find(['id=?', (int)$room['display_user_id']]);
				if ($user)
					$room['display_user'] = $this->userToChatArray($user);
			}

			if ($ackTs && $lastTs && $lastTs <= $ackTs)
				$room['bubble_unread_count'] = $room['bubble_has_unread_activity'] = 0;
		}

		return $rooms;
	}

	function getRecentPrivateRoomIndex($rooms = null)
	{
		$rooms = is_array($rooms) ? $rooms : $this->getRoomsForBubble();
		$out = [];

		foreach ($rooms as $room) {
			$userId = (int)($room['display_user_id'] ?? 0);
			if (($room['type'] ?? '') !== 'private' || $userId <= 0)
				continue;

			$activityTs = 0;
			foreach (['last_event_time', 'last_message_time', 'update_time'] as $field) {
				if (empty($room[$field]))
					continue;

				$activityTs = strtotime($room[$field]);
				if ($activityTs)
					break;
			}

			if (isset($out[$userId]) && (int)$out[$userId]['last_contact_ts'] >= (int)$activityTs)
				continue;

			$out[$userId] = [
				'room_id' => (int)$room['id'],
				'room_url' => $room['room_url'] ?? $this->roomLink((int)$room['id'], ['room_type' => 'private']),
				'last_contact_time' => $room['last_event_time'] ?? $room['last_message_time'] ?? $room['update_time'] ?? '',
				'last_contact_ts' => (int)$activityTs,
				'unread_count' => (int)($room['bubble_unread_count'] ?? $room['unread_count'] ?? 0),
			];
		}

		return $out;
	}

	function getSidebarOnlineUsers($limit = 25, $rooms = null)
	{
		$currentUserId = (int)$this->app->user->id;
		$onlineTs = strtotime('-5 minutes');
		$rooms = is_array($rooms) ? $rooms : $this->getRoomsForBubble();
		$privateRooms = $this->getRecentPrivateRoomIndex($rooms);
		$wsUsers = $this->getWsOnlineUsers();
		$out = [];

		foreach ($wsUsers as $user) {
			$userId = (int)($user['id'] ?? 0);
			if ($userId <= 0 || $userId === 1 || $userId === $currentUserId)
				continue;

			$out[$userId] = $user + [
				'is_admin' => 0,
				'last_request_time' => '',
				'last_request_ts' => 0,
				'last_request_ago' => '',
				'is_recent_online' => 0,
				'is_ws_online' => 1,
				'has_private_room' => 0,
				'room_id' => 0,
				'room_url' => '',
				'last_contact_time' => '',
				'last_contact_ts' => 0,
				'unread_count' => 0,
			];
		}

		$onlineSince = date('Y-m-d H:i:s', $onlineTs);
		$users = GW_User::singleton()->findAll(
			"id NOT IN (1, {$currentUserId}) AND active=1 AND removed=0 AND last_request_time >= '" . GW::db()->escape($onlineSince) . "'",
			['order' => 'is_admin DESC, last_request_time DESC, id DESC', 'limit' => max((int)$limit * 4, 60)]
		);

		foreach ($users as $user) {
			$userId = (int)$user->id;
			if ($userId <= 0)
				continue;

			if (!isset($out[$userId])) {
				$tmp = $this->userToChatArray($user);
				$tmp['connection_count'] = 0;
				$tmp['online_via'] = 'last_request';
				$out[$userId] = $tmp + [
					'is_admin' => 0,
					'last_request_time' => '',
					'last_request_ts' => 0,
					'last_request_ago' => '',
					'is_recent_online' => 0,
					'is_ws_online' => 0,
					'has_private_room' => 0,
					'room_id' => 0,
					'room_url' => '',
					'last_contact_time' => '',
					'last_contact_ts' => 0,
					'unread_count' => 0,
				];
			}

			$out[$userId]['is_admin'] = !empty($user->is_admin) ? 1 : 0;
			$out[$userId]['last_request_time'] = (string)$user->last_request_time;
			$out[$userId]['last_request_ts'] = $user->last_request_time ? (int)strtotime($user->last_request_time) : 0;
			$out[$userId]['last_request_ago'] = $out[$userId]['last_request_ts'] ? GW_Math_Helper::uptime(max(0, time() - $out[$userId]['last_request_ts']), 1) : '';
			$out[$userId]['is_recent_online'] = !empty($out[$userId]['last_request_ts']) && $out[$userId]['last_request_ts'] >= $onlineTs ? 1 : 0;
			$this->addSidebarDebugInfo($out[$userId], $user);
		}

		foreach ($privateRooms as $userId => $meta) {
			if (!isset($out[$userId]))
				continue;

			$out[$userId]['has_private_room'] = 1;
			$out[$userId]['room_id'] = (int)$meta['room_id'];
			$out[$userId]['room_url'] = (string)$meta['room_url'];
			$out[$userId]['last_contact_time'] = (string)$meta['last_contact_time'];
			$out[$userId]['last_contact_ts'] = (int)$meta['last_contact_ts'];
			$out[$userId]['unread_count'] = (int)$meta['unread_count'];
		}

		$out = array_values($out);

		usort($out, function ($a, $b) {
			$aBucket = !empty($a['is_admin']) ? 0 : 1;
			$bBucket = !empty($b['is_admin']) ? 0 : 1;

			if ($aBucket !== $bBucket)
				return $aBucket <=> $bBucket;

			$aHasRoom = !empty($a['has_private_room']) ? 1 : 0;
			$bHasRoom = !empty($b['has_private_room']) ? 1 : 0;

			if ($aHasRoom !== $bHasRoom)
				return $bHasRoom <=> $aHasRoom;

			$aActivity = (int)($a['last_contact_ts'] ?? 0);
			$bActivity = (int)($b['last_contact_ts'] ?? 0);

			if ($aActivity !== $bActivity)
				return $bActivity <=> $aActivity;

			$aUnread = (int)($a['unread_count'] ?? 0);
			$bUnread = (int)($b['unread_count'] ?? 0);

			if ($aUnread !== $bUnread)
				return $bUnread <=> $aUnread;

			$aWs = (($a['online_via'] ?? '') === 'ws') ? 1 : 0;
			$bWs = (($b['online_via'] ?? '') === 'ws') ? 1 : 0;

			if ($aWs !== $bWs)
				return $bWs <=> $aWs;

			$aReq = (int)($a['last_request_ts'] ?? 0);
			$bReq = (int)($b['last_request_ts'] ?? 0);

			if ($aReq !== $bReq)
				return $bReq <=> $aReq;

			$aConn = (int)($a['connection_count'] ?? 0);
			$bConn = (int)($b['connection_count'] ?? 0);

			if ($aConn !== $bConn)
				return $bConn <=> $aConn;

			return strcasecmp((string)($a['name'] ?? $a['username'] ?? ''), (string)($b['name'] ?? $b['username'] ?? ''));
		});

		foreach ($out as &$item) {
			$item['is_ws_online'] = !empty($item['is_ws_online']) || (($item['online_via'] ?? '') === 'ws');
			$item['is_recent_online'] = !empty($item['last_request_ts']) && (int)$item['last_request_ts'] >= $onlineTs ? 1 : 0;
		}

		return array_slice($out, 0, max((int)$limit, 1));
	}

	function enrichRoomForUi($room)
	{
		if (!$room)
			return null;

		$data = $this->svc()->roomToArray($room, (int)$this->app->user->id);
		$data['display_user'] = null;
		$displayUserId = (int)($data['display_user_id'] ?? 0);

		if (($data['type'] ?? '') === 'private' && $displayUserId > 0) {
			$user = GW_User::singleton()->find(['id=?', $displayUserId]);

			if ($user) {
				$tmp = $this->userToChatArray($user);
				$tmp['recently_online'] = $user->online ? 1 : 0;
				$data['display_user'] = $tmp;
			}
		}

		return $data;
	}

	function getDefaultRoom()
	{
		$svc = $this->svc();
		$currentUserId = (int)$this->app->user->id;
		$list = GW_Chat_Room::singleton()->findAll("type='group' AND title='General' AND is_active=1", ['order' => 'id ASC', 'limit' => 1]);
		$room = $list ? array_shift($list) : null;

		if (!$room)
			$room = $svc->createGroupRoom($currentUserId, 'General', [$currentUserId], 10000);

		$room->saveValues([
			'is_active' => 1,
			'room_history_limit' => 10000,
			'update_time' => date('Y-m-d H:i:s'),
		]);
		$svc->ensureMembership($room->id, $currentUserId, 'member');

		return $room;
	}

	function resolveRoomForChat($roomId)
	{
		$roomId = (int)$roomId;

		if ($roomId > 0) {
			$room = $this->svc()->getRoom($roomId);

			if (!$room || !(int)$room->is_active)
				throw new Exception('Room not found');

			$this->svc()->joinRoom($roomId, (int)$this->app->user->id);
			return $room;
		}

		return $this->getDefaultRoom();
	}

	function getReactFullStatus()
	{
		if ($this->reactFullStatusCache !== null)
			return $this->reactFullStatusCache;

		$status = $this->getReactHealthStatus(true);

		if (empty($status['ok']))
			return $this->reactFullStatusCache = [];

		$data = json_decode((string)$status['body'], true);
		return $this->reactFullStatusCache = (is_array($data) ? $data : []);
	}

	function getRoomPresenceUsers($roomId)
	{
		$roomId = (int)$roomId;
		$status = $this->getReactFullStatus();
		$list = $status['room_subscribers'][$roomId] ?? [];
		$userIds = [];

		foreach ((array)$list as $item) {
			$userId = (int)($item['id'] ?? 0);

			if ($userId > 0)
				$userIds[] = $userId;
		}

		$userIds = array_values(array_unique($userIds));
		$users = GW_User::singleton()->findAll($userIds ? GW_DB::inCondition('id', $userIds) : 'id=0', ['key_field' => 'id']);
		$out = [];
		
		

		foreach ($userIds as $userId) {
			$user = $users[$userId] ?? null;
			$meta = null;

			foreach ((array)$list as $item) {
				if ((int)($item['id'] ?? 0) === $userId) {
					$meta = $item;
					break;
				}
			}
			
			

			$tmp = $user ? $this->userToChatArray($user) : [
				'id' => $userId,
				'username' => (string)($meta['username'] ?? ''),
				'name' => (string)($meta['name'] ?? ('User #' . $userId)),
				'image_url' => '',
			];
			$tmp['connection_count'] = (int)($meta['connection_count'] ?? 0);
			$out[] = $tmp;
		}

		$fallbackIds = $this->svc()->getRoomUserIds($roomId, true);
		if ($fallbackIds) {
			$onlineSince = date('Y-m-d H:i:s', strtotime('-5 minutes'));
			$fallbackUsers = GW_User::singleton()->findAll(
				GW_DB::inCondition('id', $fallbackIds) . " AND active=1 AND removed=0 AND last_request_time >= '" . GW::db()->escape($onlineSince) . "'",
				['key_field' => 'id']
			);

			foreach ($fallbackUsers as $user) {
				$userId = (int)$user->id;
				$found = false;

				foreach ($out as $item) {
					if ((int)$item['id'] === $userId) {
						$found = true;
						break;
					}
				}

				if ($found)
					continue;

				$tmp = $this->userToChatArray($user);
				$tmp['connection_count'] = 0;
				$tmp['online_via'] = 'last_request';
				$out[] = $tmp;
			}
		}

		return $out;
	}

	function getWsOnlineUsers()
	{
		$status = $this->getReactFullStatus();
		$list = $status['online_users'] ?? [];
		$userIds = [];

		foreach ((array)$list as $item) {
			$userId = (int)($item['id'] ?? 0);

			if ($userId > 0)
				$userIds[] = $userId;
		}

		$userIds = array_values(array_unique($userIds));
		$users = GW_User::singleton()->findAll($userIds ? GW_DB::inCondition('id', $userIds) : 'id=0', ['key_field' => 'id']);
		$out = [];

		foreach ((array)$list as $item) {
			$userId = (int)($item['id'] ?? 0);

			if ($userId <= 0)
				continue;

			$user = $users[$userId] ?? null;
			$tmp = $user ? $this->userToChatArray($user) : [
				'id' => $userId,
				'username' => (string)($item['username'] ?? ''),
				'name' => (string)($item['name'] ?? ('User #' . $userId)),
				'image_url' => '',
			];
			$tmp['connection_count'] = (int)($item['connection_count'] ?? 0);
			$tmp['online_via'] = 'ws';
			$tmp['is_admin'] = ($user && !empty($user->is_admin)) ? 1 : 0;
			$tmp['last_request_time'] = $user ? (string)$user->last_request_time : '';
			$tmp['last_request_ts'] = ($user && $user->last_request_time) ? (int)strtotime($user->last_request_time) : 0;
			$this->addSidebarDebugInfo($tmp, $user);
			$out[$userId] = $tmp;
		}

		return array_values($out);
	}

	function parseUserIds($value)
	{
		if (is_array($value))
			return array_values(array_unique(array_map('intval', $value)));

		$value = trim((string)$value);
		if ($value === '')
			return [];

		return array_values(array_unique(array_map('intval', preg_split('/[\s,;]+/', $value))));
	}

	function protocolTestResult($name, $ok, $details = null, $type = 'assert')
	{
		return [
			'test' => $name,
			'ok' => $ok ? 1 : 0,
			'type' => $type,
			'details' => $this->normalizeProtocolDump($details),
		];
	}

	function normalizeProtocolDump($value)
	{
		if (is_array($value)) {
			foreach ($value as $k => $v)
				$value[$k] = $this->normalizeProtocolDump($v);

			return $value;
		}

		if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00')
			return '';

		return $value;
	}

	function findProtocolPeerUserId($excludeUserId)
	{
		$excludeUserId = (int)$excludeUserId;
		$row = GW::db()->fetch_row("
			SELECT id
			FROM gw_users
			WHERE active=1
			  AND removed=0
			  AND id!=".$excludeUserId."
			ORDER BY is_admin DESC, id ASC
			LIMIT 1
		");

		return !empty($row['id']) ? (int)$row['id'] : 0;
	}

	function viewDefault()
	{
		return $this->viewList();
	}

	function viewList()
	{
		return $this->common_viewList();
	}

	function viewChatbubble()
	{
		if (!$this->isReactServiceEnabled())
			return false;

		$this->tpl_vars['ws_path'] = $this->buildWsPath();
		$this->tpl_vars['http_endpoint'] = $this->app->app_base . $this->app->ln . '/users/chat';
		$this->tpl_vars['wss_log_to_console'] = $this->chatConsoleDebugEnabled() ? 1 : 0;
		$this->tpl_vars['chat_list_url'] = $this->roomLink(0);
		$this->tpl_vars['new_private_url'] = $this->app->buildUri($this->module_path_clean, ['act' => 'doNewPrivate']);
		$this->tpl_vars['new_room_url'] = $this->app->buildUri($this->module_path_clean, ['act' => 'doNewRoom']);
	}

	function viewTestLiveChatProtocol()
	{
		$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

		$this->tpl_vars['ws_path'] = $this->buildWsPath();
		$this->tpl_vars['http_endpoint'] = $this->app->app_base . $this->app->ln . '/users/chat';
		$this->tpl_vars['current_username'] = $this->app->user->username;
		$this->tpl_vars['current_user_id'] = (int)$this->app->user->id;
		$this->tpl_vars['requested_room_id'] = (int)($_GET['room_id'] ?? 0);
		$this->tpl_vars['wss_log_to_console'] = $this->chatConsoleDebugEnabled() ? 1 : 0;
		$this->tpl_vars['uses_secure_ws'] = $isHttps ? 1 : 0;
	}

	function viewRoom()
	{
		$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
		$roomId = (int)($_GET['id'] ?? 0);
		$roomType = (string)($_GET['room_type'] ?? '');

		if ($roomId > 0) {
			$room = $this->svc()->getRoom($roomId);

			if (!$room || !(int)$room->is_active) {
				$this->setError('Wrong room / user not in list');
				$this->app->jump('/');
				return false;
			}

			if ((string)$room->type === 'private' && !$this->svc()->getMembership($roomId, (int)$this->app->user->id, false)) {
				$this->setError('Wrong room / user not in list');
				$this->app->jump('/');
				return false;
			}
		}

		$this->tpl_vars['ws_path'] = $this->buildWsPath();
		$this->tpl_vars['http_endpoint'] = $this->app->app_base . $this->app->ln . '/users/chat';
		$this->tpl_vars['current_username'] = $this->app->user->username;
		$this->tpl_vars['current_user_id'] = (int)$this->app->user->id;
		$this->tpl_vars['requested_room_id'] = $roomId;
		$this->tpl_vars['requested_room_type'] = $roomType;
		$this->tpl_vars['wss_log_to_console'] = $this->chatConsoleDebugEnabled() ? 1 : 0;
		$this->tpl_vars['uses_secure_ws'] = $isHttps ? 1 : 0;
	}

	protected function buildWsPath()
	{
		return GW_WebSocket_Helper2::buildWsPath();
	}

	function viewUserimage()
	{
		$userId = (int)($_REQUEST['id'] ?? 0);
		$user = $userId ? GW_User::singleton()->find(['id=?', $userId]) : null;

		header('Content-Type: text/plain; charset=utf-8');
		die($this->userAvatarUrl($user));
	}

	function reactHealthUrl()
	{
		return GW_WebSocket_Helper2::healthUrl();
	}

	function reactServiceName()
	{
		return GW::s('REACTPHP_WS/SYSTEMD_SERVICE') ?: 'artistdb-reactphp-ws.service';
	}

	function reactFallbackStartCmd()
	{
		$phpCli = GW::s('PHP_CLI_LOCATION') ?: '/usr/bin/php';
		$script = GW::s('DIR/ROOT') . 'applications/cli/reactphpserver.php';
		$logFile = GW::s('DIR/LOGS') . 'reactphpserver.log';

		return escapeshellcmd($phpCli)
			. ' -d display_startup_errors=0 -d display_errors=0 -d log_errors=1 -d html_errors=0 '
			. escapeshellarg($script)
			. ' >>' . escapeshellarg($logFile) . ' 2>&1 &';
	}

	function getReactHealthStatus($full = false)
	{
		if (!$this->isReactServiceEnabled()) {
			return [
				'ok' => false,
				'disabled' => 1,
				'status_line' => 'disabled by users/chat config',
				'body' => '',
				'url' => $this->reactHealthUrl() . ($full ? '?full=1' : ''),
			];
		}

		$url = $this->reactHealthUrl() . ($full ? '?full=1' : '');
		$timeout = $full ? 0.45 : 0.25;
		$ctx = stream_context_create([
			'http' => [
				'timeout' => $timeout,
				'ignore_errors' => true,
			],
		]);

		$started = microtime(true);
		$body = @file_get_contents($url, false, $ctx);
		$elapsedMs = (int)round((microtime(true) - $started) * 1000);
		$headers = $http_response_header ?? [];
		$statusLine = $headers[0] ?? '';

		return [
			'ok' => $body !== false && strpos($statusLine, '200') !== false,
			'status_line' => $statusLine,
			'body' => is_string($body) ? trim($body) : '',
			'url' => $url,
			'elapsed_ms' => $elapsedMs,
			'timeout' => $timeout,
		];
	}

	function notifyReactHealthAdmins($subject, $message)
	{
		if (!(int)(GW::s('REACTPHP_WS/NOTIFY_HEALTH_ADMINS') ?: 0))
			return;

		$users = GW_User::singleton()->findAll('active=1 AND removed=0');

		foreach ($users as $user) {
			if (!$user->isRoot())
				continue;

			GW_Message::singleton()->message([
				'to' => $user->id,
				'subject' => $subject,
				'message' => $message,
				'level' => 15,
				'group' => false,
				'escape' => true,
			]);
		}
	}

	function restartReactPhpServer()
	{
		$currentHealth = $this->getReactHealthStatus(false);

		if (!empty($currentHealth['ok'])) {
			return [
				'method' => 'already_running',
				'cmd' => '',
				'output' => 'Health endpoint is already OK; not starting duplicate process.',
				'exit_code' => 0,
				'health' => $currentHealth,
			];
		}

		$serviceName = $this->reactServiceName();

		$allowSystemctl = (bool)(GW::s('REACTPHP_WS/ALLOW_SYSTEMCTL_RESTART') ?: 0);
		$canUseSystemctl = $allowSystemctl
			&& function_exists('posix_geteuid')
			&& (int)@posix_geteuid() === 0
			&& trim((string)@shell_exec('command -v systemctl 2>/dev/null'));

		if ($canUseSystemctl) {
			$systemctlPath = trim((string)@shell_exec('command -v systemctl 2>/dev/null'));
			$cmd = $systemctlPath . ' restart ' . escapeshellarg($serviceName) . ' 2>&1';
			$output = [];
			$exitCode = 0;
			exec($cmd, $output, $exitCode);
			$outputText = trim(implode("\n", $output));

			if ($exitCode === 0) {
				return [
					'method' => 'systemctl',
					'cmd' => $cmd,
					'output' => $outputText,
					'exit_code' => $exitCode,
				];
			}
		}

		$cmd = $this->reactFallbackStartCmd();
		$output = [];
		$exitCode = 0;
		exec($cmd, $output, $exitCode);

		return [
			'method' => 'fallback_cli',
			'cmd' => $cmd,
			'output' => trim(implode("\n", $output)),
			'exit_code' => $exitCode,
		];
	}

	function killReactPhpServer()
	{
		$pkillPath = trim((string)@shell_exec('command -v pkill 2>/dev/null'));

		if (!$pkillPath) {
			return [
				'cmd' => '',
				'output' => 'pkill not available',
				'exit_code' => 127,
			];
		}

		$killCmd = $pkillPath . ' -f ' . escapeshellarg('applications/cli/reactphpserver.php') . ' 2>&1';
		$killOutput = [];
		$killExit = 0;
		exec($killCmd, $killOutput, $killExit);

		return [
			'cmd' => $killCmd,
			'output' => trim(implode("\n", $killOutput)),
			'exit_code' => $killExit,
		];
	}

	function doReactRestartNow()
	{
		try {
			if (!$this->isReactServiceEnabled())
				$this->jsonError('ReactPHP websocket is disabled in users config', 409);

			$killed = $this->killReactPhpServer();
			usleep(500000);

			$restart = $this->restartReactPhpServer();
			sleep(2);
			$health = $this->getReactHealthStatus(false);

			$this->jsonResponse([
				'ok' => $health['ok'] ? 1 : 0,
				'killed' => $killed,
				'restart' => $restart,
				'health_after' => $health,
			], $health['ok'] ? 200 : 500);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 500);
		}
	}

	function doReactStartNow()
	{
		try {
			if (!$this->isReactServiceEnabled())
				$this->jsonError('ReactPHP websocket is disabled in users config', 409);

			$restart = $this->restartReactPhpServer();
			sleep(2);
			$health = $this->getReactHealthStatus(false);

			$this->jsonResponse([
				'ok' => $health['ok'] ? 1 : 0,
				'start' => $restart,
				'health_after' => $health,
			], $health['ok'] ? 200 : 500);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 500);
		}
	}

	function doReactStopNow()
	{
		try {
			$killed = $this->killReactPhpServer();
			usleep(500000);
			$health = $this->getReactHealthStatus(false);

			$this->jsonResponse([
				'ok' => $health['ok'] ? 0 : 1,
				'killed' => $killed,
				'health_after' => $health,
			], $health['ok'] ? 500 : 200);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 500);
		}
	}

	function doReactHealCheck()
	{
		try {
			if (!$this->isReactServiceEnabled())
				$this->jsonResponse(['ok' => 1, 'state' => 'disabled', 'health' => $this->getReactHealthStatus(false)]);

			$temp = GW_Temp_Data::singleton();
			$previousState = $temp->readValue(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status');
			$status = $this->getReactHealthStatus(false);

			if ($status['ok']) {
				$temp->store(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status', 'ok', '2 day');

				if (in_array($previousState, ['fail', 'fail_restarted'], true)) {
					$this->notifyReactHealthAdmins(
						'ReactPHP websocket recovered',
						'ReactPHP websocket service health check recovered at ' . date('Y-m-d H:i:s')
					);
				}

				$this->jsonResponse([
					'ok' => 1,
					'state' => 'ok',
					'health' => $status,
				]);
			}

			$temp->store(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status', 'fail', '2 day');
			$temp->store(
				GW_USER_SYSTEM_ID,
				'reactphp_ws',
				'last_failure_debug',
				json_encode([
					'time' => date('Y-m-d H:i:s'),
					'status_line' => $status['status_line'],
					'body' => $status['body'],
				], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
				'2 day'
			);

			$killed = $this->killReactPhpServer();
			usleep(500000);
			$restart = $this->restartReactPhpServer();
			sleep(2);

			$statusAfterRestart = $this->getReactHealthStatus(false);

			if ($statusAfterRestart['ok']) {
				$temp->store(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status', 'fail_restarted', '30 minute');

				if ($previousState !== 'fail_restarted') {
					$this->notifyReactHealthAdmins(
						'ReactPHP websocket restarted',
						'ReactPHP websocket service was unhealthy and has been restarted successfully at ' . date('Y-m-d H:i:s')
					);
				}

				$this->jsonResponse([
					'ok' => 1,
					'state' => 'fail_restarted',
					'health_before' => $status,
					'killed' => $killed,
					'restart' => $restart,
					'health_after' => $statusAfterRestart,
				]);
			}

			if ($previousState !== 'fail') {
				$this->notifyReactHealthAdmins(
					'ReactPHP websocket failure',
					"ReactPHP websocket service health check failed and restart did not recover it.\n" .
					'Health status: ' . ($statusAfterRestart['status_line'] ?: 'no response')
				);
			}

			$this->jsonResponse([
				'ok' => 0,
				'state' => 'fail',
				'health_before' => $status,
				'killed' => $killed,
				'restart' => $restart,
				'health_after' => $statusAfterRestart,
			], 500);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 500);
		}
	}

	function doReactDebugStatus()
	{
		try {
			$temp = GW_Temp_Data::singleton();
			$health = $this->getReactHealthStatus(true);

			$this->jsonResponse([
				'ok' => $health['ok'] ? 1 : 0,
				'enabled' => $this->isReactServiceEnabled() ? 1 : 0,
				'health' => $health,
				'stored_state' => $temp->readValue(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status'),
				'last_failure_debug' => $temp->readValue(GW_USER_SYSTEM_ID, 'reactphp_ws', 'last_failure_debug'),
				'service_name' => $this->reactServiceName(),
				'health_url' => $this->reactHealthUrl(),
				'fallback_start_cmd' => $this->reactFallbackStartCmd(),
				'log_file' => GW::s('DIR/LOGS') . 'reactphpserver.log',
			], $health['ok'] ? 200 : 500);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 500);
		}
	}

	function getReactWsWidgetStatus()
	{
		$temp = GW_Temp_Data::singleton();
		$health = $this->getReactHealthStatus(true);
		$storedState = (string)$temp->readValue(GW_USER_SYSTEM_ID, 'reactphp_ws', 'health_status');
		$statusLine = (string)($health['status_line'] ?? '');

		if (!empty($health['ok'])) {
			return [
				'state' => 'healthy',
				'title' => 'ReactPHP WS healthy' . ($statusLine ? ' | ' . $statusLine : ''),
			];
		}

		if ($storedState === 'fail_restarted') {
			return [
				'state' => 'warning',
				'title' => 'ReactPHP WS restarted, checking recovery' . ($statusLine ? ' | ' . $statusLine : ''),
			];
		}

		if (!$this->isReactServiceEnabled()) {
			return [
				'state' => 'error',
				'title' => 'ReactPHP WS disabled',
			];
		}

		return [
			'state' => 'error',
			'title' => 'ReactPHP WS offline' . ($statusLine ? ' | ' . $statusLine : ''),
		];
	}

	function doWsSessionDebug()
	{
		$authSessionKey = GW::s('ADMIN/AUTH_SESSION_KEY') ?: 'cms_auth';
		$sessionId = session_id();
		$sessionPath = session_save_path();
		$sessionFile = rtrim($sessionPath ?: '', '/') . '/sess_' . $sessionId;

		$this->jsonResponse([
			'ok' => 1,
			'sapi' => php_sapi_name(),
			'session_name' => session_name(),
			'session_id' => $sessionId,
			'session_save_path' => $sessionPath,
			'session_file' => $sessionFile,
			'session_file_exists' => $sessionPath ? is_file($sessionFile) : null,
			'auth_session_key' => $authSessionKey,
			'session_keys' => array_keys((array)$_SESSION),
			'auth_payload' => $_SESSION[$authSessionKey] ?? null,
			'current_user' => $this->app->user ? [
				'id' => (int)$this->app->user->id,
				'username' => $this->app->user->username,
			] : null,
		]);
	}

	function doLiveChatProtocol()
	{
		$results = [];
		$svc = $this->svc();
		$currentUserId = (int)$this->app->user->id;
		$room = null;
		$privateRoom = null;
		$peerUserId = 0;

		try {
			$health = $this->getReactHealthStatus(false);
			$results[] = $this->protocolTestResult('react_health', $health['ok'], $health);
		} catch (Exception $e) {
			$results[] = $this->protocolTestResult('react_health', false, $e->getMessage());
		}

		try {
			$rooms = $svc->getMyRooms($currentUserId);
			$results[] = $this->protocolTestResult('get_my_rooms', is_array($rooms), ['count' => is_array($rooms) ? count($rooms) : null]);
		} catch (Exception $e) {
			$results[] = $this->protocolTestResult('get_my_rooms', false, $e->getMessage());
		}

		try {
			$room = $svc->createGroupRoom($currentUserId, 'PHP Protocol Test '.date('Y-m-d H:i:s'), [], 3);
			$results[] = $this->protocolTestResult('create_group_room', !!$room && (int)$room->id > 0, $room ? $svc->roomToArray($room, $currentUserId) : null);
		} catch (Exception $e) {
			$results[] = $this->protocolTestResult('create_group_room', false, $e->getMessage());
		}

		if ($room) {
			try {
				$info = $svc->getRoomInfo($room->id, $currentUserId);
				$results[] = $this->protocolTestResult('get_room_info', (int)$info['id'] === (int)$room->id, $info);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('get_room_info', false, $e->getMessage());
			}

			try {
				$svc->joinRoom($room->id, $currentUserId);
				$membership = $svc->getMembership($room->id, $currentUserId, true);
				$results[] = $this->protocolTestResult('join_room', !!$membership, $membership ? $membership->toArray() : null);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('join_room', false, $e->getMessage());
			}

			try {
				$list = $svc->loadMessages($room->id, $currentUserId, 0, 50);
				$results[] = $this->protocolTestResult('load_messages_initial', is_array($list), ['count' => is_array($list) ? count($list) : null]);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('load_messages_initial', false, $e->getMessage());
			}

			$sentPackets = [];
			try {
				for ($i = 1; $i <= 4; $i++) {
					$sentPackets[] = $svc->sendMessage($room->id, $currentUserId, 'PHP protocol message '.$i.' '.time(), ['source' => 'backend']);
				}

				$results[] = $this->protocolTestResult(
					'send_message',
					isset($sentPackets[0]['action']) && $sentPackets[0]['action'] === 'chat_message',
					$sentPackets[0] ?? null
				);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('send_message', false, $e->getMessage());
			}

			try {
				$listAfter = $svc->loadMessages($room->id, $currentUserId, 0, 50);
				$lastMessage = end($listAfter);
				$results[] = $this->protocolTestResult(
					'load_messages_after_send',
					is_array($listAfter) && count($listAfter) >= 1,
					[
						'count' => is_array($listAfter) ? count($listAfter) : null,
						'last_message' => $lastMessage ?: null
					]
				);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('load_messages_after_send', false, $e->getMessage());
			}

			try {
				$listTrim = $svc->loadMessages($room->id, $currentUserId, 0, 50);
				$results[] = $this->protocolTestResult(
					'group_history_limit',
					count($listTrim) === 3,
					['expected' => 3, 'actual' => count($listTrim)]
				);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('group_history_limit', false, $e->getMessage());
			}

			try {
				$lastPacket = end($sentPackets);
				$lastMessageId = (int)($lastPacket['message_id'] ?? 0);
				$seenPacket = $svc->markSeen($room->id, $currentUserId, $lastMessageId);
				$membership = $svc->getMembership($room->id, $currentUserId, false);
				$results[] = $this->protocolTestResult(
					'mark_seen',
					$seenPacket['action'] === 'chat_seen' && (int)$membership->last_seen_message_id === $lastMessageId,
					['packet' => $seenPacket, 'membership' => $membership ? $membership->toArray() : null]
				);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('mark_seen', false, $e->getMessage());
			}

			try {
				$typingPacket = $svc->typing($room->id, $currentUserId, true);
				$results[] = $this->protocolTestResult('typing_start', $typingPacket['action'] === 'chat_typing', $typingPacket);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('typing_start', false, $e->getMessage());
			}

			try {
				$typingStopPacket = $svc->typing($room->id, $currentUserId, false);
				$results[] = $this->protocolTestResult('typing_stop', $typingStopPacket['action'] === 'chat_stop_typing', $typingStopPacket);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('typing_stop', false, $e->getMessage());
			}

			try {
				$svc->leaveRoom($room->id, $currentUserId);
				$membership = $svc->getMembership($room->id, $currentUserId, false);
				$results[] = $this->protocolTestResult('leave_room', $membership && !(int)$membership->is_active, $membership ? $membership->toArray() : null);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('leave_room', false, $e->getMessage());
			}

			try {
				$svc->joinRoom($room->id, $currentUserId);
				$membership = $svc->getMembership($room->id, $currentUserId, true);
				$results[] = $this->protocolTestResult('rejoin_room', !!$membership, $membership ? $membership->toArray() : null);
			} catch (Exception $e) {
				$results[] = $this->protocolTestResult('rejoin_room', false, $e->getMessage());
			}
		}

		try {
			$peerUserId = $this->findProtocolPeerUserId($currentUserId);
			if ($peerUserId) {
				$privateRoom = $svc->getOrCreatePrivateRoom($currentUserId, $peerUserId);
				$results[] = $this->protocolTestResult(
					'open_private_room',
					$privateRoom && $privateRoom->type === 'private',
					$privateRoom ? $svc->roomToArray($privateRoom, $currentUserId) : null
				);

				$privatePacket = $svc->sendMessage($privateRoom->id, $currentUserId, 'PHP private protocol test '.time(), ['source' => 'backend']);
				$privateMessages = $svc->loadMessages($privateRoom->id, $currentUserId, 0, 50);
				$results[] = $this->protocolTestResult(
					'private_room_history',
					$privatePacket['action'] === 'chat_message' && count($privateMessages) >= 1,
					['packet' => $privatePacket, 'count' => count($privateMessages)]
				);
			} else {
				$results[] = $this->protocolTestResult('open_private_room', false, 'No second active user found for private room test', 'skip');
			}
		} catch (Exception $e) {
			$results[] = $this->protocolTestResult('open_private_room', false, $e->getMessage());
		}

		$results[] = $this->protocolTestResult(
			'ws_user_online_notification',
			false,
			'Manual WS-only check. Requires browser test page or dedicated websocket client listener.',
			'manual_ws'
		);
		$results[] = $this->protocolTestResult(
			'ws_user_offline_notification',
			false,
			'Manual WS-only check. Last-socket close event cannot be confirmed from plain PHP request alone.',
			'manual_ws'
		);
		$results[] = $this->protocolTestResult(
			'ws_room_user_joined_notification',
			false,
			'Manual WS-only check. Backend confirms room state and packet structure, but not live socket delivery.',
			'manual_ws'
		);
		$results[] = $this->protocolTestResult(
			'ws_room_user_left_notification',
			false,
			'Manual WS-only check. Backend confirms room state and packet structure, but not live socket delivery.',
			'manual_ws'
		);

		d::dump($results);
		exit;
	}

	function doGetMyRooms()
	{
		try {
			$this->jsonResponse([
				'ok' => 1,
				'rooms' => $this->svc()->getMyRooms((int)$this->app->user->id)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doChatBubbleData()
	{
		try {
			$timing = [];
			$startedAll = microtime(true);
			$mark = function ($label, $started) use (&$timing) {
				$timing[$label . '_ms'] = (int)round((microtime(true) - $started) * 1000);
			};

			$started = microtime(true);
			$rooms = $this->getRoomsForBubble();
			$mark('rooms', $started);

			$started = microtime(true);
			$onlineUsers = $this->getSidebarOnlineUsers(25, $rooms);
			$mark('online_users', $started);

			$unreadTotal = 0;
			$onlineCount = 0;
			$reactWsStatus = null;

			foreach ($rooms as $room)
				$unreadTotal += (int)($room['bubble_unread_count'] ?? 0);

			foreach ($onlineUsers as $user) {
				if (!empty($user['is_recent_online']))
					$onlineCount++;
			}

			if ($this->app->user && $this->app->user->isRoot()) {
				$started = microtime(true);
				$reactWsStatus = $this->getReactWsWidgetStatus();
				$mark('react_status', $started);
			}

			$timing['total_ms'] = (int)round((microtime(true) - $startedAll) * 1000);

			if ($this->chatDebugEnabled())
				error_log('GWChatBubbleData timing user_id='.(int)$this->app->user->id.' '.json_encode($timing, JSON_UNESCAPED_SLASHES));

			$response = [
				'ok' => 1,
				'rooms' => $rooms,
				'online_users' => $onlineUsers,
				'online_count' => $onlineCount,
				'react_ws_status' => $reactWsStatus,
				'unread_total' => $unreadTotal,
			];

			if ($this->chatDebugEnabled())
				$response['_debug_timing'] = $timing;

			$this->jsonResponse($response);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doToggleConfigShowRequestUri()
	{
		if (!$this->app->user || (int)$this->app->user->id !== 9)
			Navigator::jump($this->getSafeReturnUri());

		$key = 'gw_users/onlinechat_show_last_request_uri';
		$current = (int)GW_Config::singleton()->get($key);
		GW_Config::singleton()->set($key, $current ? 0 : 1);

		Navigator::jump($this->getSafeReturnUri());
	}

	function doToogleConfigShowRequestUri()
	{
		$this->doToggleConfigShowRequestUri();
	}

	function doMarkChatbubbleAcknowledgeTime()
	{
		try {
			$time = date('Y-m-d H:i:s');
			$user = $this->currentUser();
			$user->set('keyval/chatbubble_aknowledged_at', $time);
			$user->updateChanged();

			$this->jsonResponse([
				'ok' => 1,
				'acknowledged_at' => $time,
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doGetRoom()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$this->jsonResponse([
				'ok' => 1,
				'room' => $this->svc()->getRoomInfo($roomId, (int)$this->app->user->id)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doOpenPrivateRoom()
	{
		try {
			$otherUserId = (int)($_REQUEST['user_id'] ?? 0);
			$room = $this->svc()->getOrCreatePrivateRoom((int)$this->app->user->id, $otherUserId);

			$this->jsonResponse([
				'ok' => 1,
				'room' => $this->svc()->roomToArray($room, (int)$this->app->user->id),
				'room_url' => $this->roomLink($room->id, ['room_type' => 'private'])
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doNewPrivate()
	{
		$form = [
			'fields' => [
				'user_id' => [
					'type' => 'select_ajax',
					'modpath' => 'users/usr',
					'preload' => 1,
					'options' => [],
					'source_args' => ['is_admin' => 1, 'onlineshow'=>1, 'order_last_request'=>1],
					'required' => 1,
				],
			],
			'cols'=>2
		];

		if (!($answers = $this->prompt($form, 'Pasirinkite pašnekovą')))
			return false;

		$userIds = $this->parseUserIds($answers['user_id'] ?? []);
		$otherUserId = (int)($userIds ? reset($userIds) : ($answers['user_id'] ?? 0));

		if ($otherUserId <= 0)
			throw new Exception('Nepasirinktas pašnekovas');

		$room = $this->svc()->getOrCreatePrivateRoom((int)$this->app->user->id, $otherUserId);
		$this->jump($this->roomLink($room->id, ['room_type' => 'private']));
	}

	function doNewRoom()
	{
		$form = [
			'fields' => [
				'title' => [
					'type' => 'text',
					'required' => 1,
				],
				'user_ids' => [
					'type' => 'multiselect_ajax',
					'modpath' => 'users/usr',
					'preload' => 1,
					'options' => [],
					'source_args' => ['is_admin' => 1],
				],
			],
		];

		if (!($answers = $this->prompt($form, 'Sukurkite naują pokalbio kambarį')))
			return false;

		$room = $this->svc()->createGroupRoom(
			(int)$this->app->user->id,
			(string)$answers['title'],
			$this->parseUserIds($answers['user_ids'] ?? []),
			10000
		);

		$this->jump($this->roomLink($room->id, ['room_type' => 'group']));
	}

	function doCreateGroupRoom()
	{
		try {
			$title = $_REQUEST['title'] ?? '';
			$userIds = $this->parseUserIds($_REQUEST['user_ids'] ?? []);
			$historyLimit = $_REQUEST['room_history_limit'] ?? 1000;

			$room = $this->svc()->createGroupRoom((int)$this->app->user->id, $title, $userIds, $historyLimit);

			$this->jsonResponse([
				'ok' => 1,
				'room' => $this->svc()->roomToArray($room, (int)$this->app->user->id)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doJoinRoom()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$this->svc()->joinRoom($roomId, (int)$this->app->user->id);

			$this->jsonResponse([
				'ok' => 1,
				'room' => $this->svc()->getRoomInfo($roomId, (int)$this->app->user->id)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doLeaveRoom()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$this->svc()->leaveRoom($roomId, (int)$this->app->user->id);

			$this->jsonResponse([
				'ok' => 1,
				'room_id' => $roomId
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doLoadMessages()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$beforeMessageId = (int)($_REQUEST['before_message_id'] ?? 0);
			$afterMessageId = (int)($_REQUEST['after_message_id'] ?? 0);
			$limit = (int)($_REQUEST['limit'] ?? 50);

			$this->jsonResponse([
				'ok' => 1,
				'room_id' => $roomId,
				'messages' => $this->svc()->loadMessages($roomId, (int)$this->app->user->id, $beforeMessageId, $limit, $afterMessageId)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doRoomBootstrap()
	{
		try {
			$room = $this->resolveRoomForChat((int)($_REQUEST['room_id'] ?? 0));
			$currentUserId = (int)$this->app->user->id;
			$limit = max(1, min(200, (int)($_REQUEST['limit'] ?? 100)));

			$this->jsonResponse([
				'ok' => 1,
				'room' => $this->enrichRoomForUi($room),
				'messages' => $this->svc()->loadMessages($room->id, $currentUserId, 0, $limit),
				'current_user' => $this->userToChatArray($this->currentUser()),
				'history_limit' => (int)($room->room_history_limit ?: 0),
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doRoomPresence()
	{
		try {
			$room = $this->resolveRoomForChat((int)($_REQUEST['room_id'] ?? 0));

			$this->jsonResponse([
				'ok' => 1,
				'room_id' => (int)$room->id,
				'online_users' => $this->getRoomPresenceUsers($room->id),
				'ws_online_users' => $this->getWsOnlineUsers(),
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doSendMessage()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$message = $_REQUEST['message'] ?? '';
			$replyToMessageId = (int)($_REQUEST['reply_to_message_id'] ?? 0);
			$attachments = $this->svc()->prepareUploadedAttachments($roomId, (int)$this->app->user->id, $_FILES['attachments'] ?? null);

			$packet = $this->svc()->sendMessage($roomId, (int)$this->app->user->id, $message, [
				'source' => 'web',
				'reply_to_message_id' => $replyToMessageId,
				'attachments' => $attachments,
			]);
			GW_WebSocket_Helper2::notifyRoom($roomId, $packet);

			$this->jsonResponse([
				'ok' => 1,
				'message' => $packet
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doStoreChatAttachmentFile()
	{
		try {
			$expectedToken = (string)$this->svc()->getRemoteStoreConfig('remote_store_token', '');
			$token = (string)($_REQUEST['token'] ?? '');

			if (!$expectedToken || !hash_equals($expectedToken, $token))
				$this->jsonError('Bad token', 403);

			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$userId = (int)($_REQUEST['user_id'] ?? 0);
			$filename = (string)($_REQUEST['filename'] ?? 'file');
			$mime = (string)($_REQUEST['mime'] ?? '');
			$data = base64_decode((string)($_REQUEST['filedata'] ?? ''), true);

			if ($roomId <= 0 || $data === false || $data === '')
				$this->jsonError('Bad request', 400);

			$tmp = tempnam(GW::s('DIR/TEMP'), 'chat_remote_');
			file_put_contents($tmp, $data);

			try {
				$meta = $this->svc()->storeChatAttachmentLocal($roomId, $userId, $tmp, $filename, $mime);
			} finally {
				@unlink($tmp);
			}

			$this->jsonResponse([
				'ok' => 1,
				'file' => $meta,
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doMarkSeen()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$lastMessageId = (int)($_REQUEST['last_message_id'] ?? 0);
			$packet = $this->svc()->markSeen($roomId, (int)$this->app->user->id, $lastMessageId);

			if (empty($packet['_no_broadcast']))
				GW_WebSocket_Helper2::notifyRoom($roomId, $packet, (int)$this->app->user->id);

			$this->jsonResponse([
				'ok' => 1,
				'packet' => $packet
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doTyping()
	{
		try {
			$roomId = (int)($_REQUEST['room_id'] ?? 0);
			$typing = !empty($_REQUEST['typing']);

			$this->jsonResponse([
				'ok' => 1,
				'packet' => $this->svc()->typing($roomId, (int)$this->app->user->id, $typing)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}

	function doToggleReaction()
	{
		try {
			$messageId = (int)($_REQUEST['message_id'] ?? 0);
			$reaction = (string)($_REQUEST['reaction'] ?? '');

			$this->jsonResponse([
				'ok' => 1,
				'packet' => $this->svc()->toggleMessageReaction($messageId, (int)$this->app->user->id, $reaction)
			]);
		} catch (Exception $e) {
			$this->jsonError($e->getMessage(), 400);
		}
	}
}
