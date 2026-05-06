<?php

class GW_WebSocket_Helper2
{
	static function chatConfigValue($key)
	{
		return GW_Config::singleton()->get('users__chat/' . $key);
	}

	static function cfg()
	{
		$enabledOverride = self::chatConfigValue('chatws_enabled');

		return [
			'enabled' => $enabledOverride === null || $enabledOverride === '' ? (int)GW::s('CHATWS/ENABLED') : (int)$enabledOverride,
			'transport' => (string)(GW::s('CHATWS/TRANSPORT') ?: 'reactphp'),
			'host' => (string)(GW::s('CHATWS/HOST') ?: '127.0.0.1'),
			'port' => (int)(GW::s('CHATWS/PORT') ?: 9051),
			'path' => (string)(GW::s('CHATWS/PATH') ?: '/ws'),
			'health_port' => (int)(GW::s('CHATWS/HEALTH_PORT') ?: 9052),
			'health_url' => (string)(GW::s('CHATWS/HEALTH_URL') ?: 'http://127.0.0.1:9052/healthz'),
		];
	}

	static function enabled()
	{
		$cfg = self::cfg();
		return !empty($cfg['enabled']);
	}

	static function getFrontConfig()
	{
		if (!self::enabled())
			return false;

		return self::cfg();
	}

	static function buildWsPath($sessionId = null, $sessionPath = null)
	{
		if (!self::enabled())
			return '';

		$params = [
			'GWSESSID' => $sessionId ?: session_id(),
		];

		$sessionPath = $sessionPath === null ? (string)session_save_path() : (string)$sessionPath;
		if ($sessionPath !== '')
			$params['GWSESSPATH'] = $sessionPath;

		return (self::cfg()['path'] ?: '/ws') . '?' . http_build_query($params);
	}

	static function notifyUser($username, $message)
	{
		if (!self::enabled())
			return false;

		$cfg = self::cfg();

		// ReactPHP push from backend process is not implemented yet.
		error_log('GW_WebSocket_Helper2 notifyUser transport='.$cfg['transport'].' not implemented for '.$username);
		return false;
	}

	static function controlUrl($path)
	{
		$cfg = self::cfg();
		$healthUrl = $cfg['health_url'] ?: 'http://127.0.0.1:' . ($cfg['health_port'] ?: 9052) . '/healthz';
		$parts = parse_url($healthUrl);

		if (empty($parts['scheme']) || empty($parts['host']))
			return '';

		$url = $parts['scheme'] . '://' . $parts['host'];
		if (!empty($parts['port']))
			$url .= ':' . $parts['port'];

		return $url . $path;
	}

	static function postControl($path, array $payload)
	{
		if (!self::enabled())
			return false;

		$url = self::controlUrl($path);
		if (!$url)
			return false;

		$token = (string)(GW::s('CHATWS/CONTROL_TOKEN') ?: GW::s('REACTPHP_WS/CONTROL_TOKEN') ?: '');
		if ($token !== '')
			$payload['token'] = $token;

		$body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$ctx = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => "Content-Type: application/json\r\n",
				'content' => $body,
				'timeout' => 0.5,
				'ignore_errors' => true,
			],
		]);

		$response = @file_get_contents($url, false, $ctx);
		if ($response === false)
			return false;

		$data = json_decode($response, true);
		return is_array($data) && !empty($data['ok']);
	}

	static function notifyRoom($roomId, array $packet, $excludeUserId = 0)
	{
		return self::postControl('/broadcast_room', [
			'room_id' => (int)$roomId,
			'exclude_user_id' => (int)$excludeUserId,
			'packet' => $packet,
		]);
	}

	static function fastChanMessage($channame, $message)
	{
		if (!self::enabled())
			return false;

		$cfg = self::cfg();

		// Channel push transport will be added when backend-to-ReactPHP bridge is implemented.
		error_log('GW_WebSocket_Helper2 fastChanMessage transport='.$cfg['transport'].' not implemented for '.$channame);
		return false;
	}
}
