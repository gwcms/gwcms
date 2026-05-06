<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class GW_Android_Push_Notif
{
	const WEBPUSH_KEY_PREFIX = 'webpush_subscription/';

	static function normalizeHost($host)
	{
		$host = strtolower(trim((string)$host));
		$host = preg_replace('/:\d+$/', '', $host);
		return $host;
	}

	// Grąžina vartotojo išsaugotas Web Push prenumeratas iš ext saugyklos.
	static function getWebPushSubscriptions($user)
	{
		$user = self::userFromUserid($user);

		if (!$user)
			return [];

		$list = $user->get('ext')->get(self::WEBPUSH_KEY_PREFIX . '%', true);
		$result = [];

		foreach ($list as $key => $json) {
			$data = json_decode($json, true);

			if (!is_array($data) || empty($data['endpoint']))
				continue;

			$result[$key] = $data;
		}

		return $result;
	}

	// Sugeneruoja unikalų ext raktą pagal browser endpoint'ą.
	static function makeWebPushKey($endpoint)
	{
		return self::WEBPUSH_KEY_PREFIX . md5($endpoint);
	}

	// Išsaugo arba atnaujina konkretaus vartotojo Web Push prenumeratą.
	static function saveWebPushSubscription($user, $subscription)
	{
		$user = self::userFromUserid($user);

		if (!$user || empty($subscription['endpoint']))
			return false;

		$subscription['site_host'] = self::normalizeHost($_SERVER['HTTP_HOST'] ?? '');
		$subscription['site_origin'] = Navigator::isSecure() ? 'https://' : 'http://';
		$subscription['site_origin'] .= ($_SERVER['HTTP_HOST'] ?? '');
		$subscription['insert_time'] = date('Y-m-d H:i:s');
		$key = self::makeWebPushKey($subscription['endpoint']);

		$user->get('ext')->replace($key, json_encode($subscription));

		return $key;
	}

	static function hasWebPushSubscriptionOnHost($user, $host)
	{
		if (!GW::s('MULTISITE'))
			return false;

		$user = self::userFromUserid($user);
		$host = self::normalizeHost($host);

		if (!$user || !$host)
			return false;

		$hasLegacySubscriptionWithoutHost = false;

		foreach (self::getWebPushSubscriptions($user) as $item) {
			$itemHost = self::normalizeHost($item['site_host'] ?? '');

			if ($itemHost && $itemHost === $host)
				return true;

			if (!$itemHost)
				$hasLegacySubscriptionWithoutHost = true;
		}

		// Backward compatibility: old subscriptions were stored before site_host
		// metadata existed, so treat hostless records as main-host records.
		$mainHost = self::normalizeHost(GW::s('MAIN_HOST') ?: parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST));
		if ($hasLegacySubscriptionWithoutHost && $mainHost && $host === $mainHost)
			return true;

		return false;
	}

	// Pašalina konkrečią Web Push prenumeratą pagal endpoint'ą.
	static function removeWebPushSubscription($user, $endpoint)
	{
		$user = self::userFromUserid($user);

		if (!$user || !$endpoint)
			return false;

		$user->get('ext')->replace(self::makeWebPushKey($endpoint), null);

		return true;
	}

	// Suformuoja browser notification payload iš GW_Message įrašo.
	static function buildWebPushPayload($message)
	{
		
		$image = GW::$context->app->site ? GW::$context->app->site->favico : null;
		$ln = strtolower(GW::$context->app->ln ?? 'lt');
		$url = Navigator::getBase(true) . $ln . '/users/messages/' . $message->id . '/view';
		$icon = rtrim(GW::s('SITE_URL'), '/') . '/applications/admin/static/img/logo_push_messages.png';

		if ($image && !empty($image->id))
			$icon = rtrim(GW::s('SITE_URL'), '/') . '/tools/imga/' . $image->id . '?size=128x128&method=crop';

		return [
			'title' => $message->subject,
			'body' => trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $message->message)), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
			'icon' => $icon,
			'tag' => 'gw-message-' . $message->id,
			'data' => ['url' => $url],
		];
	}

	// Paruošia Minishlink WebPush klientą pagal sistemos VAPID raktus.
	static function getWebPushClient()
	{
		$public = trim((string) GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY'));
		$private = trim((string) GW_Config::singleton()->get('sys/VAPID_PRIVATE_KEY'));

		if (!$public || !$private)
			return false;

		return new WebPush([
			'VAPID' => [
				'subject' => GW::s('SITE_URL'),
				'publicKey' => $public,
				'privateKey' => $private,
			],
		]);
	}

	// Išsiunčia modernų Web Push payload į visas vartotojo browser prenumeratas.
	static function pushWeb($user, $payload)
	{
		$user = self::userFromUserid($user);

		if (!$user)
			return false;

		$subscriptions = self::getWebPushSubscriptions($user);

		if (!$subscriptions)
			return false;

		$webPush = self::getWebPushClient();

		if (!$webPush)
			return false;

		$reports = [];

		foreach ($subscriptions as $key => $subscriptionData) {
			try {
				$report = $webPush->sendOneNotification(
					Subscription::create($subscriptionData),
					json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
				);

				$reports[$key] = [
					'success' => $report->isSuccess(),
					'reason' => $report->getReason(),
				];

				if (!$report->isSuccess() && in_array($report->getReason(), ['410 Gone', '404 Not Found'])) {
					$user->get('ext')->replace($key, null);
				}
			} catch (Throwable $e) {
				$reports[$key] = [
					'success' => false,
					'reason' => $e->getMessage(),
				];
			}
		}

		return $reports;
	}

	// Siunčia push tik tiems vartotojams, kurie nelaikomi online.
	static function pushIfNotOnline($user_id)
	{
		$online_if_last_request_newer_than = date('Y-m-d H:i:s', strtotime('-6 minute'));

		$extracond = ' AND ' . GW_DB::prepare_query(['last_request_time < ?', $online_if_last_request_newer_than]);

		if ($user = self::userFromUserid($user_id, $extracond))
			return self::push($user);
	}

	// Paverčia user id į aktyvų vartotojo objektą arba grąžina perduotą objektą.
	static function userFromUserid($user, $extra_cond = '')
	{
		if (is_numeric($user))
			$user = GW_User::singleton()->find(['id=? AND active=1 ' . $extra_cond, $user]);

		return $user;
	}

	/**
	 * 
	 * @param type $user - user_id or user object
	 */
	static function push($user)
	{
		$user = self::userFromUserid($user);

		if (!$user)
			return false;

		//var_dump($user);

		$regids = $user->get('ext')->get('android_subscription');

		foreach ($regids as $idx => $regid)
			if (strpos($regid, 'mozilla.com') !== false) {
				unset($regids[$idx]);
				self::pushFirefox($regid);
			}

		if (!$regids)
			return false;

		//panaikinti indexu tarpus kad eitu paeiliui
		$regids = array_merge($regids);


		$api_key = GW_Config::singleton()->get('sys/google_api_access_key');

		$headers = ['Authorization: key=' . $api_key, 'Content-Type: application/json'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['registration_ids' => $regids]));
		$result = curl_exec($ch);
		curl_close($ch);

		header('Content-type: text/plain');

		$data = json_decode($result, true);

		self::checkForInvalidRegIds($user, $data, $regids);

		GW::$lgr->msg("PUSH_MSGS msg to $user->id (" . count($regids) . ") response: " . $result);

		return $data;
	}

	// Palaiko seną Firefox push kelią, kai subscription ateina per mozilla endpoint'ą.
	static function pushFirefox($regid)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $regid);
		curl_setopt($ch, CURLOPT_PUT, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);

		//returns empty
	}

	static function checkForInvalidRegIds($user, $data, $regids)
	{
		if (isset($data['failure']) && $data['failure'] > 0 && isset($data['results'])) {
			foreach ($data['results'] as $idx => $info) {
				if (isset($info['error']) && $info['error'] == 'NotRegistered') {
					$user->get('ext')->deleteKeyVal('android_subscription', $regids[$idx]);
					GW::$lgr->msg("PUSH_MSGS $user->id removing: " . $regids[$idx]);
				}
			}
		}
	}

	static function getRegistrationId($str)
	{
		return str_replace("https://android.googleapis.com/gcm/send/", "", $str);
	}
}
