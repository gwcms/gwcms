<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class Module_Userspushsubscriptions extends GW_Common_Module
{
	public $default_view = 'list';

	function init()
	{
		
		
		if (!$this->app->user)
			die('no access');

		
		$this->model = GW_User::singleton();
		
		parent::init();
		
		
	}

	function getResolvedUser()
	{
		if (isset($_GET['id'])) {
			$user = $this->getDataObjectById();

			return $user;
		}

		return $this->app->user;
	}


	function truncateText($value, $limit = 90)
	{
		$value = trim((string)$value);

		if (mb_strlen($value) <= $limit)
			return $value;

		return mb_substr($value, 0, $limit - 1) . '...';
	}

	function parseUserAgentInfo($userAgentRaw)
	{
		$default = [
			'browser_name' => 'Unknown browser',
			'browser_version' => '',
			'browser_label' => 'Unknown browser',
			'browser_icon_class' => 'fa fa-globe',
			'device_type' => 'Desktop',
			'os_name' => '',
			'os_label' => '',
			'device_model' => '',
			'user_agent_pretty' => trim((string)$userAgentRaw),
		];

		$userAgentRaw = trim((string)$userAgentRaw);

		if ($userAgentRaw === '')
			return $default;

		$data = json_decode($userAgentRaw, true);

		if (!is_array($data))
			return $default;

		$browser = is_array($data['browser'] ?? null) ? $data['browser'] : [];
		$os = is_array($data['os'] ?? null) ? $data['os'] : [];
		$features = is_array($data['features'] ?? null) ? $data['features'] : [];
		$userAgentData = is_array($data['user_agent_data'] ?? null) ? $data['user_agent_data'] : [];
		$name = trim((string)($browser['fullName'] ?? ''));
		$version = trim((string)($browser['version'] ?? ''));
		$code = strtolower(trim((string)($browser['name'] ?? '')));
		$rawUa = trim((string)($data['raw_user_agent'] ?? ''));
		$osName = trim((string)($os['fullName'] ?? ''));
		$deviceModel = trim((string)($userAgentData['model'] ?? ''));
		$platform = trim((string)($userAgentData['platform'] ?? ''));

		if ($name === '')
			$name = 'Unknown browser';

		$iconMap = [
			'chrome' => 'fa fa-chrome',
			'chromium' => 'fa fa-chrome',
			'firefox' => 'fa fa-firefox',
			'safari' => 'fa fa-safari',
			'edge' => 'fa fa-edge',
			'opera' => 'fa fa-opera',
			'ie' => 'fa fa-internet-explorer',
			'internet explorer' => 'fa fa-internet-explorer',
		];

		$iconClass = $iconMap[$code] ?? 'fa fa-globe';
		$deviceType = 'Desktop';

		if (!empty($features['tv'])) {
			$deviceType = 'TV';
		} elseif ($rawUa !== '') {
			$isTablet = preg_match('/ipad|tablet|playbook|silk/i', $rawUa)
				|| (preg_match('/android/i', $rawUa) && !preg_match('/mobile/i', $rawUa));

			if ($isTablet)
				$deviceType = 'Tablet';
			elseif (!empty($features['mobile']) || preg_match('/iphone|ipod|mobile/i', $rawUa))
				$deviceType = 'Mobile';
		} elseif (!empty($features['mobile'])) {
			$deviceType = 'Mobile';
		}

		if ($platform !== '') {
			if (preg_match('/android/i', $platform))
				$osName = 'Android';
			elseif (preg_match('/ios/i', $platform))
				$osName = 'iOS';
			elseif (preg_match('/windows/i', $platform))
				$osName = 'Windows';
			elseif (preg_match('/mac/i', $platform))
				$osName = 'macOS';
			elseif (preg_match('/linux/i', $platform) && $osName === '')
				$osName = 'Linux';
		}

		if ($rawUa !== '') {
			if (preg_match('/android/i', $rawUa))
				$osName = 'Android';
			elseif ($osName === '' && preg_match('/iphone|ipad|ipod/i', $rawUa))
				$osName = 'iOS';
			elseif ($osName === '' && preg_match('/windows/i', $rawUa))
				$osName = 'Windows';
			elseif ($osName === '' && preg_match('/mac os x|macintosh/i', $rawUa))
				$osName = 'macOS';
			elseif ($osName === '' && preg_match('/linux/i', $rawUa))
				$osName = 'Linux';
		}

		if ($deviceModel === '' && $rawUa !== '') {
			if (preg_match('/;\s*([A-Z0-9\-]+(?:\s?[A-Z0-9\-]+)*)\s+Build\//i', $rawUa, $matches))
				$deviceModel = trim($matches[1]);
		}

		return [
			'browser_name' => $name,
			'browser_version' => $version,
			'browser_label' => trim($name . ' ' . $version),
			'browser_icon_class' => $iconClass,
			'device_type' => $deviceType,
			'os_name' => $osName,
			'os_label' => $osName,
			'device_model' => $deviceModel,
			'user_agent_pretty' => $this->truncateText($userAgentRaw, 140),
		];
	}

	function getSubscriptionMapForUser($user)
	{
		$list = GW_Android_Push_Notif::getWebPushSubscriptions($user);

		uasort($list, function($a, $b){
			return strcmp((string)($b['insert_time'] ?? ''), (string)($a['insert_time'] ?? ''));
		});

		return $list;
	}

	function getSubscriptionByKey($user, $key)
	{
		$key = trim((string)$key);
		$list = $this->getSubscriptionMapForUser($user);

		if ($key === '' || !isset($list[$key]))
			throw new Exception('Subscription not found');

		return $list[$key];
	}

	function normalizeSubscriptionRow($key, $data)
	{
		$data = is_array($data) ? $data : [];
		$endpoint = (string)($data['endpoint'] ?? '');
		$url = parse_url($endpoint);
		$host = (string)($url['host'] ?? '');
		$path = (string)($url['path'] ?? '');
		$userAgentInfo = $this->parseUserAgentInfo((string)($data['user_agent'] ?? ''));

		return [
			'storage_key' => (string)$key,
			'endpoint' => $endpoint,
			'endpoint_host' => $host,
			'endpoint_path' => $this->truncateText($path, 70),
			'site_host' => (string)($data['site_host'] ?? ''),
			'site_origin' => (string)($data['site_origin'] ?? ''),
			'insert_time' => (string)($data['insert_time'] ?? ''),
			'user_agent' => $userAgentInfo['user_agent_pretty'],
			'browser_name' => $userAgentInfo['browser_name'],
			'browser_version' => $userAgentInfo['browser_version'],
			'browser_label' => $userAgentInfo['browser_label'],
			'browser_icon_class' => $userAgentInfo['browser_icon_class'],
			'device_type' => $userAgentInfo['device_type'],
			'os_name' => $userAgentInfo['os_name'],
			'os_label' => $userAgentInfo['os_label'],
			'device_model' => $userAgentInfo['device_model'],
			'ip' => (string)($data['ip'] ?? ''),
			'has_expiration_time' => !empty($data['expirationTime']) ? 1 : 0,
			'expiration_time' => (string)($data['expirationTime'] ?? ''),
			'raw_json' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
			'test_url' => $this->buildUri(false, [
				'act' => 'doTestSubscription',
				'key' => (string)$key,
			]),
			'remove_url' => $this->buildUri(false, [
				'act' => 'doRemoveSubscription',
				'key' => (string)$key,
			]),
		];
	}

	function prepareRowsForUser($user)
	{
		$list = $this->getSubscriptionMapForUser($user);

		$rows = [];

		foreach ($list as $key => $data)
			$rows[] = $this->normalizeSubscriptionRow($key, $data);

		$this->tpl_vars['target_user'] = $user;
		$this->tpl_vars['subscriptions'] = $rows;
		$this->tpl_vars['is_my_subscriptions'] = ((int)$user->id === (int)$this->app->user->id) ? 1 : 0;
		$this->tpl_vars['test_my_subscription_url'] = $this->buildUri(false, [
			'act' => 'doTestMySubscriptionAndClear',
		]);
	}

	function viewList()
	{
		$user = $this->getResolvedUser();
		$this->prepareRowsForUser($user);

		return ['item' => (object)['title' => 'Push subscriptions']];
	}

	function viewManagemysubscriptions()
	{
		$this->prepareRowsForUser($this->getResolvedUser());
		$this->tpl_file_name = $this->tpl_dir . 'list';

		return ['item' => (object)['title' => 'Manage my push subscriptions']];
	}

	function doRemoveSubscription()
	{
		try {
			$user = $this->getResolvedUser();
			$subscription = $this->getSubscriptionByKey($user, $_REQUEST['key'] ?? '');

			try {
				$webPush = new WebPush([
					'VAPID' => [
						'subject' => GW::s('SITE_URL'),
						'publicKey' => GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY'),
						'privateKey' => GW_Config::singleton()->get('sys/VAPID_PRIVATE_KEY'),
					],
				]);

				$payload = [
					'title' => 'Push subscription cleared',
					'body' => 'Your push subscription was cleared by user ' . (int)$this->app->user->id,
					'icon' => '/applications/admin/static/img/logo_push_messages.png',
					'data' => ['url' => '/lt/users/profile'],
				];

				$webPush->sendOneNotification(
					Subscription::create($subscription),
					json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
				);
			} catch (Throwable $pushError) {
				error_log('Push notification before subscription removal failed: ' . $pushError->getMessage());
			}

			GW_Android_Push_Notif::removeWebPushSubscription($user, $subscription['endpoint'] ?? '');
			$this->setPlainMessage('Push subscription removed');
		} catch (Exception $e) {
			$this->setError($e->getMessage());
		}

		unset($_GET['act']);
		unset($_GET['key']);

		$this->app->jump();
	}

	function doTestSubscription()
	{
		try {
			$user = $this->getResolvedUser();
			$subscriptionData = $this->getSubscriptionByKey($user, $_REQUEST['key'] ?? '');

			$subscription = Subscription::create($subscriptionData);
			$webPush = new WebPush([
				'VAPID' => [
					'subject' => GW::s('SITE_URL'),
					'publicKey' => GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY'),
					'privateKey' => GW_Config::singleton()->get('sys/VAPID_PRIVATE_KEY'),
				],
			]);

			$payload = [
				'title' => 'Push subscription test',
				'body' => 'This test was sent to one selected browser subscription.',
				'icon' => '/applications/admin/static/img/logo_push_messages.png',
				'data' => ['url' => '/lt/users/messages'],
			];

			$report = $webPush->sendOneNotification(
				$subscription,
				json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
			);

			if ($report->isSuccess())
				$this->setPlainMessage('Test push sent successfully');
			else
				$this->setError('Test push failed: ' . $report->getReason());
		} catch (Exception $e) {
			$this->setError($e->getMessage());
		} catch (Throwable $e) {
			$this->setError('Test push failed: ' . $e->getMessage());
		}

		

		unset($_GET['act']);
		unset($_GET['key']);		
		$this->app->jump();
	}

	function doTestMySubscriptionAndClear()
	{
		header('Content-Type: application/json; charset=utf-8');

		try {
			$user = $this->app->user;
			$subscriptionData = json_decode($_REQUEST['data'] ?? '', true);
			$endpoint = trim((string)($subscriptionData['endpoint'] ?? ''));

			if ($endpoint === '')
				throw new Exception('Missing endpoint');

			$list = $this->getSubscriptionMapForUser($user);
			$matched = null;

			foreach ($list as $item) {
				if (($item['endpoint'] ?? '') === $endpoint) {
					$matched = $item;
					break;
				}
			}

			if (!$matched) {
				$this->setMessage('Your browser push subscription was removed from backend, sync done, local subscription was cleared');

				echo json_encode([
					'ok' => 1,
					'message' => 'sync_only',
				]);
				exit;
			}

			$subscription = Subscription::create($matched);
			$webPush = new WebPush([
				'VAPID' => [
					'subject' => GW::s('SITE_URL'),
					'publicKey' => GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY'),
					'privateKey' => GW_Config::singleton()->get('sys/VAPID_PRIVATE_KEY'),
				],
			]);

			$payload = [
				'title' => 'Push subscription test',
				'body' => 'This test was sent to your current browser subscription.',
				'icon' => '/applications/admin/static/img/logo_push_messages.png',
				'data' => ['url' => '/lt/users/messages'],
			];

			$report = $webPush->sendOneNotification(
				$subscription,
				json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
			);

			if (!$report->isSuccess())
				throw new Exception('Test push failed: ' . $report->getReason());

			GW_Android_Push_Notif::removeWebPushSubscription($user, $endpoint);
			$this->setMessage('Your browser push subscription was removed from backend, sync done, local subscription was cleared');

			echo json_encode([
				'ok' => 1,
				'message' => 'ok',
			]);
		} catch (Throwable $e) {
			echo json_encode([
				'ok' => 0,
				'error' => $e->getMessage(),
			]);
		}

		exit;
	}
}
