#!/usr/bin/php
<?php

if (!isset($_SERVER['REMOTE_ADDR']))
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

if (!isset($_SERVER['HTTP_CF_CONNECTING_IP']))
	$_SERVER['HTTP_CF_CONNECTING_IP'] = $_SERVER['REMOTE_ADDR'];

if (!isset($_SERVER['HTTP_HOST']))
	$_SERVER['HTTP_HOST'] = '127.0.0.1';

if (!isset($_SERVER['SERVER_PORT']))
	$_SERVER['SERVER_PORT'] = '80';

if (!isset($_SERVER['REQUEST_URI']))
	$_SERVER['REQUEST_URI'] = '/cli/reactphpserver.php';

if (!ob_get_level())
	ob_start();
include __DIR__ . '/../../init_basic.php';

use Psr\Http\Message\ServerRequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\MessageComponentInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Http\HttpServer as ReactHttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

if (php_sapi_name() !== 'cli') {
	fwrite(STDERR, "CLI only\n");
	exit(1);
}

$required = [
	'React\\EventLoop\\Loop',
	'React\\Socket\\SocketServer',
	'React\\Http\\HttpServer',
	'React\\Http\\Message\\Response',
	'Ratchet\\MessageComponentInterface',
	'Ratchet\\Server\\IoServer',
	'Ratchet\\Http\\HttpServer',
	'Ratchet\\WebSocket\\WsServer',
];

$missing = [];

foreach ($required as $class) {
	if (!class_exists($class) && !interface_exists($class))
		$missing[] = $class;
}

if ($missing) {
	fwrite(STDERR, "Missing ReactPHP / Ratchet dependencies:\n");
	foreach ($missing as $class)
		fwrite(STDERR, " - {$class}\n");

	fwrite(STDERR, "\nInstall command:\n");
	fwrite(STDERR, "composer require react/event-loop:^1.6 react/socket:^1.17 react/http:^1.11 cboden/ratchet:^0.4.4\n");
	exit(2);
}

class GW_ReactPHP_Chat_Server implements MessageComponentInterface
{
	protected $clients;
	protected $connUser = [];
	protected $connRooms = [];
	protected $roomSubs = [];
	protected $startTime;
	protected $debug = false;
	protected $logFile;
	protected $recentEvents = [];
	protected $maxRecentEvents = 50;

	function __construct($opts = [])
	{
		$this->clients = new SplObjectStorage();
		$this->startTime = time();
		$this->debug = !empty($opts['debug']);
		$this->logFile = $opts['log_file'] ?? (GW::s('DIR/LOGS') . 'reactphpserver.log');
	}

	function onOpen(ConnectionInterface $conn)
	{
		try {
			$timing = [];
			$startedAll = microtime(true);
			$mark = function ($label, $started) use (&$timing) {
				$timing[$label . '_ms'] = (int)round((microtime(true) - $started) * 1000);
			};

			$request = $conn->httpRequest ?? null;
			$started = microtime(true);
			$sessionCtx = $this->extractSessionContext($request);
			$mark('extract_session', $started);
			$sessionId = $sessionCtx['id'];
			$started = microtime(true);
			$session = $this->loadSession($sessionId, $sessionCtx);
			$mark('load_session', $started);
			$authSessionKey = GW::s('ADMIN/AUTH_SESSION_KEY') ?: 'cms_auth';
			$userId = (int)($session[$authSessionKey]['user_id'] ?? 0);

			if (!$userId)
				throw new Exception('Unauthorised');

			$started = microtime(true);
			$user = GW_User::singleton()->find(['id=?', $userId]);
			$mark('load_user', $started);

			if (!$user)
				throw new Exception('User not found');

			$this->clients->attach($conn);
			$this->connUser[$conn->resourceId] = [
				'user_id' => $userId,
				'username' => $user->username,
				'name' => trim(($user->name ?? '').' '.($user->surname ?? '')) ?: $user->username,
			];
			$this->connRooms[$conn->resourceId] = [];

			$this->push($conn, 'hello', [
				'user' => [
					'id' => $userId,
					'username' => $user->username,
					'name' => $this->connUser[$conn->resourceId]['name'],
				],
				'server_time' => date('Y-m-d H:i:s'),
				'protocol' => 'gwchat.v1',
				'transport' => 'reactphp',
			]);

			$timing['total_ms'] = (int)round((microtime(true) - $startedAll) * 1000);
			$this->log('connect', [
				'resourceId' => $conn->resourceId,
				'user_id' => $userId,
				'username' => $user->username,
				'timing' => $timing,
			]);

			$this->broadcastPresence($this->connUser[$conn->resourceId], 'user_connected', $conn);
			if ($this->getUserConnectionCount($userId) === 1)
				$this->broadcastPresence($this->connUser[$conn->resourceId], 'user_online', $conn);
		} catch (Exception $e) {
			$sessionId = $sessionId ?? null;
			$session = $session ?? [];
			$authSessionKey = $authSessionKey ?? (GW::s('ADMIN/AUTH_SESSION_KEY') ?: 'cms_auth');
			$this->log('open_fail', [
				'resourceId' => $conn->resourceId,
				'error' => $e->getMessage(),
				'session_id' => $sessionId,
				'auth_session_key' => $authSessionKey,
				'session_keys' => is_array($session) ? array_keys($session) : [],
				'auth_payload' => is_array($session) ? ($session[$authSessionKey] ?? null) : null,
				'timing' => $timing ?? null,
			]);
			try {
				$conn->send(json_encode([
					'action' => 'error',
					'ok' => 0,
					'error' => $e->getMessage(),
				], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			} catch (Exception $ignored) {}
			$conn->close();
		}
	}

	function onMessage(ConnectionInterface $from, $msg)
	{
		try {
			$user = $this->getConnUser($from);
			$data = json_decode($msg, true);

			if (!is_array($data))
				throw new Exception('Invalid JSON');

			$action = $data['action'] ?? '';
			$reqId = $data['req_id'] ?? null;
			$svc = GW_Chat_Service::singleton();
			$this->log('packet_in', [
				'resourceId' => $from->resourceId,
				'user_id' => $user['user_id'],
				'action' => $action,
				'req_id' => $reqId,
				'payload' => $this->debug ? $data : $this->compactPacket($data),
			]);

			switch ($action) {
				case 'ping':
					$this->reply($from, 'pong', [
						'time' => date('Y-m-d H:i:s'),
					], $reqId);
				break;

				case 'my_rooms':
					$this->reply($from, 'rooms', [
						'rooms' => $svc->getMyRooms($user['user_id']),
					], $reqId);
				break;

				case 'open_private_room':
					$otherUserId = (int)($data['user_id'] ?? 0);
					$room = $svc->getOrCreatePrivateRoom($user['user_id'], $otherUserId);
					$roomData = $svc->roomToArray($room, $user['user_id']);
					$this->subscribeConnToRoom($from, $room->id);
					$this->reply($from, 'room', ['room' => $roomData], $reqId);
				break;

				case 'join_room':
					$roomId = (int)($data['room_id'] ?? 0);
					$joinResult = $svc->joinRoom($roomId, $user['user_id']);
					$room = $svc->getRoomInfo($roomId, $user['user_id']);
					$this->subscribeConnToRoom($from, $roomId);
					$this->reply($from, 'room_joined', ['room' => $room], $reqId);

					if (!empty($joinResult['did_join'])) {
						$this->broadcastRoom($roomId, [
							'action' => 'room_user_joined',
							'room_id' => $roomId,
							'user' => [
								'id' => $user['user_id'],
								'username' => $user['username'],
								'name' => $user['name'],
							],
						], $from);

						if (!empty($joinResult['event_packet']))
							$this->broadcastRoom($roomId, $joinResult['event_packet'], $from);
					}
				break;

				case 'leave_room':
					$roomId = (int)($data['room_id'] ?? 0);
					$leaveResult = $svc->leaveRoom($roomId, $user['user_id']);
					$this->broadcastRoom($roomId, [
						'action' => 'room_user_left',
						'room_id' => $roomId,
						'user' => [
							'id' => $user['user_id'],
							'username' => $user['username'],
							'name' => $user['name'],
						],
					], $from);
					if (!empty($leaveResult['event_packet']))
						$this->broadcastRoom($roomId, $leaveResult['event_packet'], $from);
					$this->unsubscribeConnFromRoom($from, $roomId);
					$this->reply($from, 'room_left', ['room_id' => $roomId], $reqId);
				break;

				case 'load_messages':
					$roomId = (int)($data['room_id'] ?? 0);
					$beforeMessageId = (int)($data['before_message_id'] ?? 0);
					$afterMessageId = (int)($data['after_message_id'] ?? 0);
					$limit = (int)($data['limit'] ?? 50);
					$list = $svc->loadMessages($roomId, $user['user_id'], $beforeMessageId, $limit, $afterMessageId);
					$this->reply($from, 'messages', [
						'room_id' => $roomId,
						'messages' => $list,
					], $reqId);
				break;

				case 'send_message':
					$roomId = (int)($data['room_id'] ?? 0);
					$message = (string)($data['message'] ?? '');
					$replyTo = (int)($data['reply_to_message_id'] ?? 0);
					$packet = $svc->sendMessage($roomId, $user['user_id'], $message, [
						'source' => 'web',
						'reply_to_message_id' => $replyTo,
						'attachments' => [],
					]);
					$this->reply($from, 'message_sent', [
						'message' => $packet,
					], $reqId);
					$this->broadcastRoom($roomId, $packet);
				break;

				case 'typing':
					$roomId = (int)($data['room_id'] ?? 0);
					$typing = !empty($data['typing']);
					$packet = $svc->typing($roomId, $user['user_id'], $typing);
					$this->reply($from, 'typing_ack', [
						'room_id' => $roomId,
						'typing' => $typing ? 1 : 0,
					], $reqId);
					$this->broadcastRoom($roomId, $packet, $from);
				break;

				case 'seen':
					$roomId = (int)($data['room_id'] ?? 0);
					$lastMessageId = (int)($data['last_message_id'] ?? 0);
					$packet = $svc->markSeen($roomId, $user['user_id'], $lastMessageId);
					$this->reply($from, 'seen_ack', [
						'room_id' => $roomId,
						'last_message_id' => $lastMessageId,
					], $reqId);
					if (empty($packet['_no_broadcast']))
						$this->broadcastRoom($roomId, $packet, $from);
				break;

				case 'toggle_reaction':
					$messageId = (int)($data['message_id'] ?? 0);
					$reaction = (string)($data['reaction'] ?? '');
					$packet = $svc->toggleMessageReaction($messageId, $user['user_id'], $reaction);
					$this->reply($from, 'reaction_ack', [
						'message_id' => $messageId,
						'reaction' => $reaction,
						'packet' => $packet,
					], $reqId);
					$this->broadcastRoom((int)$packet['room_id'], $packet);
					$this->broadcastRoom((int)$packet['room_id'], [
						'action' => 'chat_event',
						'room_id' => (int)$packet['room_id'],
						'event' => [
							'entry_type' => 'event',
							'entry_key' => 'e'.(int)$packet['event_id'],
							'event_id' => (int)$packet['event_id'],
							'room_id' => (int)$packet['room_id'],
							'user_id' => (int)$packet['user_id'],
							'event_type' => (string)$packet['event_type'],
							'ref_id' => (int)$packet['message_id'],
							'text' => (string)$packet['event_text'],
							'insert_time' => (string)$packet['insert_time'],
						],
					]);
				break;

				default:
					throw new Exception('Unknown action: '.$action);
			}
		} catch (Exception $e) {
			$this->reply($from, 'error', [], $data['req_id'] ?? null, $e->getMessage(), 0);
			$this->log('message_fail', [
				'resourceId' => $from->resourceId,
				'error' => $e->getMessage(),
				'raw' => $msg,
			]);
		}
	}

	function onClose(ConnectionInterface $conn)
	{
		$user = $this->connUser[$conn->resourceId] ?? null;
		$userId = $user['user_id'] ?? 0;

		$this->cleanupConn($conn);
		$this->log('close', ['resourceId' => $conn->resourceId]);

		if ($user) {
			$this->broadcastPresence($user, 'user_disconnected');
			if ($userId && $this->getUserConnectionCount($userId) === 0)
				$this->broadcastPresence($user, 'user_offline');
		}
	}

	function onError(ConnectionInterface $conn, Exception $e)
	{
		$this->log('error', [
			'resourceId' => $conn->resourceId,
			'error' => $e->getMessage(),
		]);
		$this->cleanupConn($conn);
		$conn->close();
	}

	function getStatus()
	{
		$userMap = [];

		foreach ($this->connUser as $info) {
			$userId = (int)$info['user_id'];

			if (!$userId)
				continue;

			if (empty($userMap[$userId])) {
				$userMap[$userId] = [
					'id' => $userId,
					'username' => $info['username'],
					'name' => $info['name'],
					'connection_count' => 0,
				];
			}

			$userMap[$userId]['connection_count']++;
		}

		$dbInfo = null;
		if (!empty(GW::$context->vars['db']) && is_object(GW::$context->vars['db']) && method_exists(GW::$context->vars['db'], 'getConnectionDebugInfo'))
			$dbInfo = GW::$context->vars['db']->getConnectionDebugInfo();

		return [
			'pid' => getmypid(),
			'start_time' => date('Y-m-d H:i:s', $this->startTime),
			'uptime' => time() - $this->startTime,
			'debug' => $this->debug ? 1 : 0,
			'log_file' => $this->logFile,
			'connections' => count($this->connUser),
			'users_online' => count($userMap),
			'db_connection' => $dbInfo,
			'online_users' => array_values($userMap),
			'room_subscribers' => $this->getRoomSubscriberSnapshot(),
			'memory_usage' => memory_get_usage(true),
			'rooms_active' => count($this->roomSubs),
			'recent_events' => $this->recentEvents,
		];
	}

	protected function log($label, $data = [])
	{
		$record = [
			'time' => date('Y-m-d H:i:s'),
			'label' => $label,
			'data' => $data,
		];

		$this->recentEvents[] = $record;
		if (count($this->recentEvents) > $this->maxRecentEvents)
			array_shift($this->recentEvents);

		$line = '[reactphp-ws] '.$label;
		if ($data)
			$line .= ' '.json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		echo $line."\n";

		if ($this->logFile)
			@file_put_contents($this->logFile, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
	}

	protected function send(ConnectionInterface $conn, array $packet)
	{
		$this->log('packet_out', [
			'resourceId' => $conn->resourceId,
			'action' => $packet['action'] ?? '',
			'req_id' => $packet['req_id'] ?? null,
			'ok' => $packet['ok'] ?? null,
			'payload' => $this->debug ? $packet : $this->compactPacket($packet),
		]);
		$conn->send(json_encode($packet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	protected function reply(ConnectionInterface $conn, $action, array $payload = [], $reqId = null, $error = null, $ok = 1)
	{
		$packet = [
			'action' => $action,
			'ok' => $ok ? 1 : 0,
		] + $payload;

		if ($reqId !== null)
			$packet['req_id'] = $reqId;

		if ($error !== null)
			$packet['error'] = $error;

		$this->send($conn, $packet);
	}

	protected function push(ConnectionInterface $conn, $action, array $payload = [])
	{
		$this->send($conn, ['action' => $action, 'ok' => 1] + $payload);
	}

	protected function broadcastRoom($roomId, array $packet, ConnectionInterface $excludeConn = null)
	{
		$roomId = (int)$roomId;

		if (empty($this->roomSubs[$roomId]))
			return;

		foreach ($this->roomSubs[$roomId] as $resourceId => $conn) {
			if ($excludeConn && $excludeConn->resourceId == $resourceId)
				continue;

			$this->push($conn, $packet['action'] ?? 'event', $packet);
		}
	}

	function broadcastRoomPacket($roomId, array $packet, $excludeUserId = 0)
	{
		$roomId = (int)$roomId;
		$excludeUserId = (int)$excludeUserId;
		$sent = 0;

		if (empty($this->roomSubs[$roomId]))
			return $sent;

		foreach ($this->roomSubs[$roomId] as $resourceId => $conn) {
			$userId = (int)($this->connUser[$resourceId]['user_id'] ?? 0);
			if ($excludeUserId && $userId === $excludeUserId)
				continue;

			$this->push($conn, $packet['action'] ?? 'event', $packet);
			$sent++;
		}

		return $sent;
	}

	protected function broadcastPresence(array $user, $action, ConnectionInterface $excludeConn = null)
	{
		foreach ($this->clients as $conn) {
			if ($excludeConn && $excludeConn === $conn)
				continue;

			$this->push($conn, $action, [
				'user' => [
					'id' => (int)$user['user_id'],
					'username' => $user['username'],
					'name' => $user['name'],
				],
				'connection_count' => $this->getUserConnectionCount($user['user_id']),
			]);
		}
	}

	protected function getUserConnectionCount($userId)
	{
		$count = 0;

		foreach ($this->connUser as $info)
			if ((int)$info['user_id'] === (int)$userId)
				$count++;

		return $count;
	}

	protected function subscribeConnToRoom(ConnectionInterface $conn, $roomId)
	{
		$roomId = (int)$roomId;

		if (!isset($this->roomSubs[$roomId]))
			$this->roomSubs[$roomId] = [];

		$this->roomSubs[$roomId][$conn->resourceId] = $conn;
		$this->connRooms[$conn->resourceId][$roomId] = 1;
	}

	protected function unsubscribeConnFromRoom(ConnectionInterface $conn, $roomId)
	{
		$roomId = (int)$roomId;

		unset($this->roomSubs[$roomId][$conn->resourceId]);
		unset($this->connRooms[$conn->resourceId][$roomId]);

		if (empty($this->roomSubs[$roomId]))
			unset($this->roomSubs[$roomId]);
	}

	protected function cleanupConn(ConnectionInterface $conn)
	{
		if ($this->clients->contains($conn))
			$this->clients->detach($conn);

		if (!empty($this->connRooms[$conn->resourceId])) {
			foreach (array_keys($this->connRooms[$conn->resourceId]) as $roomId)
				unset($this->roomSubs[$roomId][$conn->resourceId]);
		}

		foreach ($this->roomSubs as $roomId => $conns)
			if (!$conns)
				unset($this->roomSubs[$roomId]);

		unset($this->connRooms[$conn->resourceId], $this->connUser[$conn->resourceId]);
	}

	protected function getRoomSubscriberSnapshot()
	{
		$snapshot = [];

		foreach ($this->roomSubs as $roomId => $conns) {
			$users = [];

			foreach ($conns as $resourceId => $conn) {
				$info = $this->connUser[$resourceId] ?? null;
				$userId = (int)($info['user_id'] ?? 0);

				if (!$userId)
					continue;

				if (empty($users[$userId])) {
					$users[$userId] = [
						'id' => $userId,
						'username' => $info['username'],
						'name' => $info['name'],
						'connection_count' => 0,
					];
				}

				$users[$userId]['connection_count']++;
			}

			$snapshot[(int)$roomId] = array_values($users);
		}

		return $snapshot;
	}

	protected function getConnUser(ConnectionInterface $conn)
	{
		if (empty($this->connUser[$conn->resourceId]))
			throw new Exception('Unauthorised connection');

		return $this->connUser[$conn->resourceId];
	}

	protected function extractSessionContext($request)
	{
		if (!$request || !method_exists($request, 'getHeaderLine'))
			throw new Exception('Missing HTTP request context');

		$queryParams = method_exists($request, 'getQueryParams') ? (array)$request->getQueryParams() : [];

		if (!$queryParams && method_exists($request, 'getUri')) {
			parse_str((string)$request->getUri()->getQuery(), $queryParams);
			$queryParams = (array)$queryParams;
		}

		$sessionId = trim((string)($queryParams['GWSESSID'] ?? ''));
		$sessionPath = trim((string)($queryParams['GWSESSPATH'] ?? ''));

		if ($sessionId !== '')
			return ['id' => $sessionId, 'path' => $sessionPath];

		$cookieHeader = $request->getHeaderLine('Cookie');
		if (!$cookieHeader)
			throw new Exception('Missing Cookie header and GWSESSID');

		$cookies = [];
		foreach (explode(';', $cookieHeader) as $part) {
			$part = trim($part);
			if ($part === '' || strpos($part, '=') === false)
				continue;

			list($key, $value) = explode('=', $part, 2);
			$cookies[trim($key)] = trim($value);
		}

		$sessionName = session_name() ?: ini_get('session.name') ?: 'PHPSESSID';
		$sessionId = $cookies[$sessionName] ?? '';

		if (!$sessionId && !empty($cookies['GWSESSID']))
			$sessionId = $cookies['GWSESSID'];

		if (!$sessionId)
			throw new Exception('Missing session cookie ' . $sessionName . ' and GWSESSID');

		return ['id' => $sessionId, 'path' => $sessionPath];
	}

	protected function loadSession($sessionId, array $sessionCtx = [])
	{
		if (!$sessionId)
			throw new Exception('Empty session id');

		if ((string)ini_get('session.save_handler') === 'files' && (string)ini_get('session.serialize_handler') === 'php') {
			$file = $this->resolveSessionFileForId($sessionId, $sessionCtx);
			if (!$file)
				throw new Exception('Session file not found');

			$raw = @file_get_contents($file);
			if (!is_string($raw))
				throw new Exception('Session file unreadable');

			return $this->decodePhpSessionData($raw);
		}

		if (session_status() === PHP_SESSION_ACTIVE)
			session_write_close();

		$this->applySessionStorageForId($sessionId, $sessionCtx);
		// CLI websocket process reads session data only and must not depend on HTTP headers.
		session_cache_limiter('');
		ini_set('session.use_cookies', '0');
		ini_set('session.use_only_cookies', '0');
		$_SESSION = [];
		session_id($sessionId);
		session_start(['read_and_close' => true]);
		$data = $_SESSION;
		$_SESSION = [];

		return $data;
	}

	protected function resolveSessionFileForId($sessionId, array $sessionCtx = [])
	{
		if (!preg_match('/^[A-Za-z0-9,-]+$/', (string)$sessionId))
			return '';

		$current = (string)session_save_path();
		$hintPath = trim((string)($sessionCtx['path'] ?? ''));
		$candidates = array_filter(array_unique([
			$hintPath,
			$current,
			'/tmp',
			sys_get_temp_dir(),
			'/var/lib/php/sessions',
		]));

		foreach ($candidates as $path) {
			$file = rtrim($path, '/').'/sess_'.$sessionId;
			if (is_file($file))
				return $file;
		}

		return '';
	}

	protected function decodePhpSessionData($raw)
	{
		$out = [];
		$offset = 0;
		$length = strlen($raw);

		while ($offset < $length) {
			$pipe = strpos($raw, '|', $offset);
			if ($pipe === false)
				break;

			$key = substr($raw, $offset, $pipe - $offset);
			$valueInfo = $this->unserializeSessionValueAt($raw, $pipe + 1);
			if (!$key || !$valueInfo)
				break;

			$out[$key] = $valueInfo['value'];
			$offset = $valueInfo['next_offset'];
		}

		return $out;
	}

	protected function unserializeSessionValueAt($raw, $offset)
	{
		$length = strlen($raw);

		for ($end = $offset + 1; $end <= $length; $end++) {
			$lastChar = $raw[$end - 1] ?? '';
			if ($lastChar !== ';' && $lastChar !== '}')
				continue;

			$candidate = substr($raw, $offset, $end - $offset);
			$value = @unserialize($candidate);
			if ($value !== false || $candidate === 'b:0;') {
				return [
					'value' => $value,
					'next_offset' => $end,
				];
			}
		}

		return null;
	}

	protected function applySessionStorageForId($sessionId, array $sessionCtx = [])
	{
		$current = (string)session_save_path();
		$hintPath = trim((string)($sessionCtx['path'] ?? ''));
		$candidates = array_filter(array_unique([
			$hintPath,
			$current,
			'/tmp',
			sys_get_temp_dir(),
			'/var/lib/php/sessions',
		]));

		foreach ($candidates as $path) {
			if (!$this->sessionFileExists($path, $sessionId))
				continue;

			if ($path !== $current)
				session_save_path($path);

			return;
		}
	}

	protected function sessionFileExists($path, $sessionId)
	{
		if (!$path)
			return false;

		return is_file(rtrim($path, '/').'/sess_'.$sessionId);
	}

	protected function compactPacket(array $packet)
	{
		$out = $packet;

		if (isset($out['message']) && is_string($out['message']) && strlen($out['message']) > 120)
			$out['message'] = substr($out['message'], 0, 120) . '...';

		if (isset($out['messages']) && is_array($out['messages']))
			$out['messages'] = 'count:' . count($out['messages']);

		if (isset($out['rooms']) && is_array($out['rooms']))
			$out['rooms'] = 'count:' . count($out['rooms']);

		return $out;
	}
}

function gwReactphpVersionSignature($file)
{
	clearstatcache(true, $file);

	if (!is_file($file))
		return 'missing';

	return filemtime($file) . ':' . filesize($file) . ':' . sha1_file($file);
}

function gwReactphpLogLine($label, $data = [])
{
	$record = [
		'time' => date('Y-m-d H:i:s'),
		'label' => $label,
		'data' => $data,
	];
	$line = '[reactphp-ws] ' . $label;

	if ($data)
		$line .= ' ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	echo $line . "\n";
	@file_put_contents(GW::s('DIR/LOGS') . 'reactphpserver.log', json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
}

function gwReactphpIsSystemdManaged()
{
	return (string)getenv('INVOCATION_ID') !== '' || (string)getenv('JOURNAL_STREAM') !== '';
}

function gwReactphpScheduleSelfRestart()
{
	$phpCli = PHP_BINARY ?: (GW::s('PHP_CLI_LOCATION') ?: '/usr/bin/php');
	$script = __FILE__;
	$logFile = GW::s('DIR/LOGS') . 'reactphpserver.log';
	$args = $_SERVER['argv'] ?? [];
	array_shift($args);

	$cmd = 'sleep 1; '
		. escapeshellcmd($phpCli)
		. ' -d display_startup_errors=0 -d display_errors=0 '
		. escapeshellarg($script);

	foreach ($args as $arg)
		$cmd .= ' ' . escapeshellarg($arg);

	$cmd .= ' >>' . escapeshellarg($logFile) . ' 2>&1 &';

	@exec('/bin/sh -c ' . escapeshellarg($cmd));

	return $cmd;
}

function gwReactphpBoolValue($value)
{
	if ($value === null || $value === '')
		return null;

	if (is_bool($value))
		return $value;

	return in_array(strtolower((string)$value), ['1', 'true', 'yes', 'on'], true);
}

function gwReactphpAutorestartOnVersionChangeEnabled()
{
	$cfgValue = GW_Config::singleton()->get('users__chat/autorestart_on_version_change');
	$cfgBool = gwReactphpBoolValue($cfgValue);

	if ($cfgBool !== null)
		return $cfgBool;

	return (bool)(GW::s('REACTPHP_WS/AUTORESTART_ON_VERSION_CHANGE') ?: 0);
}

$loop = Loop::get();
$debugEnabled = in_array('--debug', $argv, true)
	|| (bool)(GW::s('REACTPHP_WS/DEBUG') ?: 0)
	|| (int)GW_Config::singleton()->get('users__chat/full_chat_debug');
$wsHost = (string)GW::s('CHATWS/HOST');
$wsPort = GW::s('CHATWS/PORT');

if ($wsHost === '') {
	fwrite(STDERR, "CHATWS/HOST is required; refusing to fall back to a default websocket host.\n");
	exit(3);
}

if ($wsPort === null || $wsPort === '' || (int)$wsPort <= 0) {
	fwrite(STDERR, "CHATWS/PORT is required; refusing to fall back to a default websocket port.\n");
	exit(3);
}

$wsPort = (int)$wsPort;
$healthPort = $wsPort + 1;
$chatServer = new GW_ReactPHP_Chat_Server([
	'debug' => $debugEnabled,
	'log_file' => GW::s('DIR/LOGS') . 'reactphpserver.log',
]);

$healthServer = new ReactHttpServer(function (ServerRequestInterface $request) use ($chatServer) {
	$path = $request->getUri()->getPath();

	if ($path === '/broadcast_room') {
		if (strtoupper($request->getMethod()) !== 'POST')
			return new Response(405, ['Content-Type' => 'application/json; charset=utf-8'], json_encode(['ok' => 0, 'error' => 'Method not allowed']));

		$data = json_decode((string)$request->getBody(), true);
		if (!is_array($data))
			return new Response(400, ['Content-Type' => 'application/json; charset=utf-8'], json_encode(['ok' => 0, 'error' => 'Invalid JSON']));

		$expectedToken = (string)(GW::s('CHATWS/CONTROL_TOKEN') ?: GW::s('REACTPHP_WS/CONTROL_TOKEN') ?: '');
		if ($expectedToken !== '' && !hash_equals($expectedToken, (string)($data['token'] ?? '')))
			return new Response(403, ['Content-Type' => 'application/json; charset=utf-8'], json_encode(['ok' => 0, 'error' => 'Bad token']));

		$sent = $chatServer->broadcastRoomPacket((int)($data['room_id'] ?? 0), (array)($data['packet'] ?? []), (int)($data['exclude_user_id'] ?? 0));
		return new Response(200, ['Content-Type' => 'application/json; charset=utf-8'], json_encode(['ok' => 1, 'sent' => $sent], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	if ($path !== '/healthz')
		return new Response(404, ['Content-Type' => 'text/plain; charset=utf-8'], "Not found\n");

	$status = $chatServer->getStatus();
	$full = $request->getQueryParams()['full'] ?? 0;

	if ($full)
		return new Response(200, ['Content-Type' => 'application/json; charset=utf-8'], json_encode($status, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

	return new Response(200, ['Content-Type' => 'text/plain; charset=utf-8'], "OK\n");
});

$wsSocket = new SocketServer($wsHost . ':' . $wsPort, [], $loop);
$healthSocket = new SocketServer($wsHost . ':' . $healthPort, [], $loop);

$healthServer->listen($healthSocket);
new IoServer(
	new HttpServer(
		new WsServer($chatServer)
	),
	$wsSocket,
	$loop
);

$versionFile = (string)(GW::s('REACTPHP_WS/VERSION_FILE') ?: (GW::s('DIR/ROOT') . 'version'));
$versionWatchInterval = (float)(GW::s('REACTPHP_WS/VERSION_WATCH_INTERVAL') ?: 5);
$versionRestartExitCode = (int)(GW::s('REACTPHP_WS/VERSION_RESTART_EXIT_CODE') ?: 75);
$versionAutorestartEnabled = gwReactphpAutorestartOnVersionChangeEnabled();
$versionSignature = $versionAutorestartEnabled ? gwReactphpVersionSignature($versionFile) : null;

if ($versionAutorestartEnabled && $versionWatchInterval > 0) {
	$loop->addPeriodicTimer($versionWatchInterval, function () use ($versionFile, $versionRestartExitCode, &$versionSignature) {
		$currentSignature = gwReactphpVersionSignature($versionFile);

		if ($currentSignature === $versionSignature)
			return;

		gwReactphpLogLine('version_changed_restart', [
			'file' => $versionFile,
			'old' => $versionSignature,
			'new' => $currentSignature,
		]);

		if (!gwReactphpIsSystemdManaged()) {
			$cmd = gwReactphpScheduleSelfRestart();
			gwReactphpLogLine('version_changed_self_restart_scheduled', [
				'cmd' => $cmd,
			]);
			exit(0);
		}

		exit($versionRestartExitCode);
	});
}

$loop->run();
