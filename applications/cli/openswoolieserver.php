#!/usr/bin/php
<?php

if (!isset($_SERVER['REMOTE_ADDR']))
	$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

if (!isset($_SERVER['HTTP_CF_CONNECTING_IP']))
	$_SERVER['HTTP_CF_CONNECTING_IP'] = $_SERVER['REMOTE_ADDR'];

if (!ob_get_level())
	ob_start();
include __DIR__ . '/../../init_basic.php';

if (php_sapi_name() !== 'cli') {
	fwrite(STDERR, "CLI only\n");
	exit(1);
}

if (!extension_loaded('openswoole')) {
	fwrite(STDERR, "OpenSwoole extension is not loaded\n");
	fwrite(STDERR, "Expected protocol target: gwchat.v1\n");
	exit(2);
}

class GW_OpenSwoole_Chat_Server
{
	protected $server;
	protected $debug = false;
	protected $logFile;
	protected $startTime;
	protected $connUser = [];
	protected $connRooms = [];
	protected $roomSubs = [];
	protected $recentEvents = [];
	protected $maxRecentEvents = 50;

	function __construct($opts = [])
	{
		$this->debug = !empty($opts['debug']);
		$this->logFile = $opts['log_file'] ?? (GW::s('DIR/LOGS') . 'openswoolieserver.log');
		$this->startTime = time();
	}

	function start()
	{
		$host = GW::s('OPENSWOOLE_WS/HOST') ?: '127.0.0.1';
		$port = (int)(GW::s('OPENSWOOLE_WS/PORT') ?: 9501);

		$this->server = new OpenSwoole\WebSocket\Server($host, $port);

		$this->server->set([
			'worker_num' => 1,
			'daemonize' => 0,
			'log_file' => $this->logFile,
		]);

		$this->server->on('Start', function() use ($host, $port) {
			$this->log('start', [
				'host' => $host,
				'port' => $port,
				'protocol' => 'gwchat.v1',
				'transport' => 'openswoole',
			]);
			echo "OpenSwoole WS server started\n";
			echo "WS: ws://{$host}:{$port}/ws\n";
			echo "Health: http://{$host}:{$port}/healthz\n";
			echo "Health full: http://{$host}:{$port}/healthz?full=1\n";
		});

		$this->server->on('Request', function($request, $response) {
			$path = $request->server['request_uri'] ?? '/';

			if ($path === '/healthz') {
				$full = !empty(($request->get ?? [])['full']);
				$status = $this->getStatus();

				if ($full) {
					$response->header('Content-Type', 'application/json; charset=utf-8');
					$response->end(json_encode($status, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
					return;
				}

				$response->header('Content-Type', 'text/plain; charset=utf-8');
				$response->end("OK\n");
				return;
			}

			if ($path !== '/ws') {
				$response->status(404);
				$response->end("Not found\n");
				return;
			}

			$response->status(426);
			$response->end("Upgrade Required\n");
		});

		$this->server->on('Open', function(OpenSwoole\WebSocket\Server $server, $request) {
			$fd = (int)$request->fd;

			try {
				$sessionCtx = $this->extractSessionContextFromRequest($request);
				$sessionId = $sessionCtx['id'];
				$session = $this->loadSession($sessionId, $sessionCtx);
				$userId = (int)($session['cms_auth']['user_id'] ?? 0);

				if (!$userId)
					throw new Exception('Unauthorised');

				$user = GW_User::singleton()->find(['id=?', $userId]);
				if (!$user)
					throw new Exception('User not found');

				$this->connUser[$fd] = [
					'user_id' => $userId,
					'username' => $user->username,
					'name' => trim(($user->first_name ?? '') . ' ' . ($user->second_name ?? '')) ?: $user->username,
				];
				$this->connRooms[$fd] = [];

				$this->push($fd, 'hello', [
					'user' => [
						'id' => $userId,
						'username' => $user->username,
						'name' => $this->connUser[$fd]['name'],
					],
					'server_time' => date('Y-m-d H:i:s'),
					'protocol' => 'gwchat.v1',
					'transport' => 'openswoole',
				]);

				$this->log('connect', [
					'fd' => $fd,
					'user_id' => $userId,
					'username' => $user->username,
				]);
			} catch (Exception $e) {
				$this->log('open_fail', ['fd' => $fd, 'error' => $e->getMessage()]);
				$this->sendPacket($fd, [
					'action' => 'error',
					'ok' => 0,
					'error' => $e->getMessage(),
				]);
				$server->disconnect($fd, 1008, 'Unauthorised');
			}
		});

		$this->server->on('Message', function(OpenSwoole\WebSocket\Server $server, $frame) {
			$fd = (int)$frame->fd;
			$msg = $frame->data;

			try {
				$user = $this->getConnUser($fd);
				$data = json_decode($msg, true);

				if (!is_array($data))
					throw new Exception('Invalid JSON');

				$action = $data['action'] ?? '';
				$reqId = $data['req_id'] ?? null;
				$svc = GW_Chat_Service::singleton();

				$this->log('packet_in', [
					'fd' => $fd,
					'user_id' => $user['user_id'],
					'action' => $action,
					'req_id' => $reqId,
					'payload' => $this->debug ? $data : $this->compactPacket($data),
				]);

				switch ($action) {
					case 'ping':
						$this->reply($fd, 'pong', ['time' => date('Y-m-d H:i:s')], $reqId);
					break;

					case 'my_rooms':
						$this->reply($fd, 'rooms', ['rooms' => $svc->getMyRooms($user['user_id'])], $reqId);
					break;

					case 'open_private_room':
						$otherUserId = (int)($data['user_id'] ?? 0);
						$room = $svc->getOrCreatePrivateRoom($user['user_id'], $otherUserId);
						$roomData = $svc->roomToArray($room, $user['user_id']);
						$this->subscribeFdToRoom($fd, $room->id);
						$this->reply($fd, 'room', ['room' => $roomData], $reqId);
					break;

					case 'join_room':
						$roomId = (int)($data['room_id'] ?? 0);
						$joinResult = $svc->joinRoom($roomId, $user['user_id']);
						$room = $svc->getRoomInfo($roomId, $user['user_id']);
						$this->subscribeFdToRoom($fd, $roomId);
						$this->reply($fd, 'room_joined', ['room' => $room], $reqId);

						if (!empty($joinResult['did_join'])) {
							$this->broadcastRoom($roomId, [
								'action' => 'room_user_joined',
								'room_id' => $roomId,
								'user' => [
									'id' => $user['user_id'],
									'username' => $user['username'],
									'name' => $user['name'],
								],
							], $fd);

							if (!empty($joinResult['event_packet']))
								$this->broadcastRoom($roomId, $joinResult['event_packet'], $fd);
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
						], $fd);
						if (!empty($leaveResult['event_packet']))
							$this->broadcastRoom($roomId, $leaveResult['event_packet'], $fd);
						$this->unsubscribeFdFromRoom($fd, $roomId);
						$this->reply($fd, 'room_left', ['room_id' => $roomId], $reqId);
					break;

					case 'load_messages':
						$roomId = (int)($data['room_id'] ?? 0);
						$beforeMessageId = (int)($data['before_message_id'] ?? 0);
						$limit = (int)($data['limit'] ?? 50);
						$list = $svc->loadMessages($roomId, $user['user_id'], $beforeMessageId, $limit);
						$this->reply($fd, 'messages', ['room_id' => $roomId, 'messages' => $list], $reqId);
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
						$this->reply($fd, 'message_sent', ['message' => $packet], $reqId);
						$this->broadcastRoom($roomId, $packet);
					break;

					case 'typing':
						$roomId = (int)($data['room_id'] ?? 0);
						$typing = !empty($data['typing']);
						$packet = $svc->typing($roomId, $user['user_id'], $typing);
						$this->reply($fd, 'typing_ack', ['room_id' => $roomId, 'typing' => $typing ? 1 : 0], $reqId);
						$this->broadcastRoom($roomId, $packet, $fd);
					break;

					case 'seen':
						$roomId = (int)($data['room_id'] ?? 0);
						$lastMessageId = (int)($data['last_message_id'] ?? 0);
						$packet = $svc->markSeen($roomId, $user['user_id'], $lastMessageId);
						$this->reply($fd, 'seen_ack', ['room_id' => $roomId, 'last_message_id' => $lastMessageId], $reqId);
						$this->broadcastRoom($roomId, $packet, $fd);
					break;

					case 'toggle_reaction':
						$messageId = (int)($data['message_id'] ?? 0);
						$reaction = (string)($data['reaction'] ?? '');
						$packet = $svc->toggleMessageReaction($messageId, $user['user_id'], $reaction);
						$this->reply($fd, 'reaction_ack', [
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
						throw new Exception('Unknown action: ' . $action);
				}
			} catch (Exception $e) {
				$reqId = $data['req_id'] ?? null;
				$this->reply($fd, 'error', [], $reqId, $e->getMessage(), 0);
				$this->log('message_fail', ['fd' => $fd, 'error' => $e->getMessage(), 'raw' => $msg]);
			}
		});

		$this->server->on('Close', function($server, $fd) {
			$this->cleanupFd((int)$fd);
			$this->log('close', ['fd' => (int)$fd]);
		});

		$this->server->start();
	}

	protected function sendPacket($fd, array $packet)
	{
		if (!$this->server->isEstablished($fd))
			return;

		$this->log('packet_out', [
			'fd' => $fd,
			'action' => $packet['action'] ?? '',
			'req_id' => $packet['req_id'] ?? null,
			'ok' => $packet['ok'] ?? null,
			'payload' => $this->debug ? $packet : $this->compactPacket($packet),
		]);

		$this->server->push($fd, json_encode($packet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	protected function reply($fd, $action, array $payload = [], $reqId = null, $error = null, $ok = 1)
	{
		$packet = ['action' => $action, 'ok' => $ok ? 1 : 0] + $payload;

		if ($reqId !== null)
			$packet['req_id'] = $reqId;

		if ($error !== null)
			$packet['error'] = $error;

		$this->sendPacket($fd, $packet);
	}

	protected function push($fd, $action, array $payload = [])
	{
		$this->sendPacket($fd, ['action' => $action, 'ok' => 1] + $payload);
	}

	protected function broadcastRoom($roomId, array $packet, $excludeFd = 0)
	{
		$roomId = (int)$roomId;

		if (empty($this->roomSubs[$roomId]))
			return;

		foreach ($this->roomSubs[$roomId] as $fd => $state) {
			if ($excludeFd && (int)$fd === (int)$excludeFd)
				continue;

			$this->push((int)$fd, $packet['action'] ?? 'event', $packet);
		}
	}

	protected function subscribeFdToRoom($fd, $roomId)
	{
		$fd = (int)$fd;
		$roomId = (int)$roomId;

		if (!isset($this->roomSubs[$roomId]))
			$this->roomSubs[$roomId] = [];

		$this->roomSubs[$roomId][$fd] = 1;
		$this->connRooms[$fd][$roomId] = 1;
	}

	protected function unsubscribeFdFromRoom($fd, $roomId)
	{
		$fd = (int)$fd;
		$roomId = (int)$roomId;

		unset($this->roomSubs[$roomId][$fd], $this->connRooms[$fd][$roomId]);

		if (empty($this->roomSubs[$roomId]))
			unset($this->roomSubs[$roomId]);
	}

	protected function cleanupFd($fd)
	{
		$fd = (int)$fd;

		if (!empty($this->connRooms[$fd])) {
			foreach (array_keys($this->connRooms[$fd]) as $roomId)
				unset($this->roomSubs[$roomId][$fd]);
		}

		foreach ($this->roomSubs as $roomId => $members)
			if (!$members)
				unset($this->roomSubs[$roomId]);

		unset($this->connRooms[$fd], $this->connUser[$fd]);
	}

	protected function getConnUser($fd)
	{
		if (empty($this->connUser[$fd]))
			throw new Exception('Unauthorised connection');

		return $this->connUser[$fd];
	}

	protected function extractSessionContextFromRequest($request)
	{
		$queryParams = (array)($request->get ?? []);
		$sessionId = trim((string)($queryParams['GWSESSID'] ?? ''));
		$sessionPath = trim((string)($queryParams['GWSESSPATH'] ?? ''));

		if ($sessionId !== '')
			return ['id' => $sessionId, 'path' => $sessionPath];

		$cookieHeader = $request->header['cookie'] ?? '';
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

		return is_file(rtrim($path, '/') . '/sess_' . $sessionId);
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

		$line = '[openswoole-ws] ' . $label;
		if ($data)
			$line .= ' ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		echo $line . "\n";

		if ($this->logFile)
			@file_put_contents($this->logFile, json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
	}

	protected function getStatus()
	{
		$userIds = [];
		foreach ($this->connUser as $info)
			$userIds[(int)$info['user_id']] = 1;

		return [
			'pid' => getmypid(),
			'start_time' => date('Y-m-d H:i:s', $this->startTime),
			'uptime' => time() - $this->startTime,
			'debug' => $this->debug ? 1 : 0,
			'log_file' => $this->logFile,
			'connections' => count($this->connUser),
			'users_online' => count($userIds),
			'rooms_active' => count($this->roomSubs),
			'memory_usage' => memory_get_usage(true),
			'recent_events' => $this->recentEvents,
		];
	}
}

$debugEnabled = in_array('--debug', $argv, true) || (bool)(GW::s('OPENSWOOLE_WS/DEBUG') ?: 0);
$server = new GW_OpenSwoole_Chat_Server([
	'debug' => $debugEnabled,
	'log_file' => GW::s('DIR/LOGS') . 'openswoolieserver.log',
]);
$server->start();
