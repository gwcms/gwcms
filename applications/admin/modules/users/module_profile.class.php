<?php


use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;


class Module_Profile extends GW_Module
{	
	function init()
	{
		//profile modulis public, su salyga yra prisilogines vartotojas
		if(!$this->app->user)
			$this->app->jump(Navigator::getBase());
		
		
		$this->model = new GW_User();
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->app->user];		
	}

	function doSave()
	{
		$this->viewDefault();
	}
	
	function viewLogout()
	{
		$this->app->auth->logout();
		$this->app->jump(GW::s('ADMIN/PATH_LOGIN'));
	}
	
	function doUpdateMyPass()
	{
		$vals=$_REQUEST['item'];
		
		$item =& $this->app->user;
		$item->setValues($vals);		
		
		$item->setValidators('change_pass_check_old');
		
		if(!$item->validate()){
			$this->setItemErrors($item);
			
			$this->processView('default');
			exit;	
		}else{
			$item->setValidators(false);
			if($item->update(Array('pass')))
				$this->setPlainMessage($this->lang['PASS_UPDATED']);
		}
		
		$this->jump();
	}
	
	function doUpdateMyProfile()
	{
		$vals=$_REQUEST['item'];
		
		$fields=Array('name','surname','email');
		
		$item =& $this->app->user;
		$item->setValues($vals);	
		$item->setValidators('update');
			
		
		if(!$item->validate()){
			$this->setItemErrors($item);
			
			$this->processView('default');
			exit;	
		}else{
			if($item->update($fields))
				$this->setPlainMessage($this->smarty->_tpl_vars['lang']['UPDATE_SUCCESS']);
		}
		
		$this->jump();		
	}
	
	function doSwitchUserReturn()
	{
		$this->app->auth->switchUserReturn();
		$this->jump();
	}

	// Išsaugo arba pašalina prisijungusio admin vartotojo browser Web Push prenumeratą.
	function doStoreSubscription()
	{
		$subscription = json_decode($_POST['data'], true);
		

		if (!isset($subscription['endpoint'])) {
			echo 'Error: not a subscription';
			return;
		}

		if(isset($_REQUEST['unsubscribe'])){
			GW_Android_Push_Notif::removeWebPushSubscription($this->app->user, $subscription['endpoint']);
			echo "Subscription cleared";
		}else{
			$userAgent = $_POST['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'];
			$userAgentData = json_decode($userAgent, true);

			if (is_array($userAgentData) && empty($userAgentData['raw_user_agent']))
				$userAgentData['raw_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

			$subscription['user_agent'] = is_array($userAgentData)
				? json_encode($userAgentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
				: $userAgent;
			$subscription['ip'] = GW::ip();
			GW_Android_Push_Notif::saveWebPushSubscription($this->app->user, $subscription);
			echo "Subscription saved";			
		}

		
		exit;
		
		/*
		
		$subscription = GW_Android_Push_Notif::getRegistrationId($_GET["subscription"]);
		$new = $this->app->user->get('ext')->insertIfNotExists('android_subscription', $subscription);

		echo "New: $new";
		echo $subscription;
		echo "\nOK";
		exit;
		 * 
		 */
	}

	function doCheckSubscriptionSync()
	{
		header('Content-Type: application/json; charset=utf-8');

		$subscription = json_decode($_REQUEST['data'] ?? '', true);
		$endpoint = trim((string)($subscription['endpoint'] ?? ''));

		if ($endpoint === '') {
			echo json_encode(['ok' => 0, 'error' => 'Missing endpoint']);
			exit;
		}

		$list = GW_Android_Push_Notif::getWebPushSubscriptions($this->app->user);
		$hasBackendSubscription = false;

		foreach ($list as $item) {
			if (($item['endpoint'] ?? '') === $endpoint) {
				$hasBackendSubscription = true;
				break;
			}
		}

		echo json_encode([
			'ok' => 1,
			'has_backend_subscription' => $hasBackendSubscription ? 1 : 0,
		]);
		exit;
	}

	function doGetMainHostPushStatus()
	{
		header('Content-Type: application/json; charset=utf-8');

		// This endpoint exists only for MULTISITE push coordination.
		// In single-site projects local host subscriptions should behave normally.
		if (!GW::s('MULTISITE')) {
			echo json_encode([
				'ok' => 1,
				'multisite' => 0,
				'main_host' => '',
				'current_host' => GW_Android_Push_Notif::normalizeHost($_SERVER['HTTP_HOST'] ?? ''),
				'is_main_host' => 1,
				'has_main_host_subscription' => 0,
				'main_host_manage_url' => '',
			]);
			exit;
		}

		$mainHost = GW_Android_Push_Notif::normalizeHost(GW::s('MAIN_HOST') ?: parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST));
		$currentHost = GW_Android_Push_Notif::normalizeHost($_SERVER['HTTP_HOST'] ?? '');

		echo json_encode([
			'ok' => 1,
			'multisite' => 1,
			'main_host' => $mainHost,
			'current_host' => $currentHost,
			'is_main_host' => ($mainHost && $currentHost === $mainHost) ? 1 : 0,
			'has_main_host_subscription' => GW_Android_Push_Notif::hasWebPushSubscriptionOnHost($this->app->user, $mainHost) ? 1 : 0,
			'main_host_manage_url' => $mainHost ? ('https://' . $mainHost . '/admin/' . $this->app->ln . '/users/userspushsubscriptions/managemysubscriptions') : '',
		]);
		exit;
	}

	function doReleaseSubscriptionOwnership()
	{
		header('Content-Type: application/json; charset=utf-8');

		$subscription = json_decode($_REQUEST['data'] ?? '', true);
		$endpoint = trim((string)($subscription['endpoint'] ?? ''));
		$ownerUserId = (int)($_REQUEST['owner_user_id'] ?? 0);

		if ($endpoint === '') {
			echo json_encode(['ok' => 0, 'error' => 'Missing endpoint']);
			exit;
		}

		if (!$ownerUserId) {
			echo json_encode(['ok' => 0, 'error' => 'Missing owner user id']);
			exit;
		}

		GW_Android_Push_Notif::removeWebPushSubscription($ownerUserId, $endpoint);

		echo json_encode(['ok' => 1]);
		exit;
	}
	
	// Išsiunčia testinį browser Web Push į pirmą aktyvią dabartinio vartotojo prenumeratą.
	function doTestNotification()
	{
		$subscriptions = GW_Android_Push_Notif::getWebPushSubscriptions($this->app->user);
		
		if(!$subscriptions)
			die('No web push subscriptions for this user');
		
		$subscriber = array_shift($subscriptions);
		$subscription = Subscription::create($subscriber);
			
		$auth = array(
		    'VAPID' => array(
			'subject' => GW::s('SITE_URL'),
			'publicKey' => GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY'), // don't forget that your public key also lives in app.js
			'privateKey' => GW_Config::singleton()->get('sys/VAPID_PRIVATE_KEY'), // in the real world, this would be in a secret file
		    ),
		);

		$webPush = new WebPush($auth);
			
		$data = [
		    'title'=>'this is title testas', 
		    'body'=>'this is body testas',
		    'icon'=>"/applications/admin/static/img/logo_push_messages.png",
		    //'image'=>"/applications/admin/static/img/logo_push_messages.png",
		    /*
		    'actions'=>[
			[
			'action'=>'1bbdaction',
			'title'=>'1do bbd',
			//'icon'=>"/applications/admin/static/img/logo_push_messages.png"
			]
		    ],
		     * 
		     */
		    'data'=>['url'=>'/lt/users/messages']
		];

		$report = $webPush->sendOneNotification($subscription, json_encode($data));
		
		d::ldump([$report, 'success'=>$report->isSuccess()]);
		exit;
		
	}

	
	function doLinkWithFb()
	{
		$comebackurlAuthgw = $this->app->buildURI(false,['act'=>'doFinishLinkWithFb'],['absolute'=>1]);
		$req_id = GW_String_Helper::getRandString(25);
		$_SESSION['adm_auth_gw_lt_req_id']=$req_id;
			
		session_commit();
		session_write_close();
		$auth_gw_url = GW::s('GW_FB_SERVICE')."?request_id=".$req_id."&redirect2=". urlencode($comebackurlAuthgw);
		header('Location: '.$auth_gw_url);		
		exit;			
	}
	
	function doUnLinkWithFb()
	{
		$this->app->user->ext->adminfbid = '';
		$this->setMessage('Facebook account Unlinked');
		$this->jump();
	}
	
	function doFinishLinkWithFb()
	{	
		$req_id = $_SESSION['adm_auth_gw_lt_req_id'];
		$dat = file_get_contents(GW::s('GW_FB_SERVICE').'?get_response='.$req_id);
		$dat = json_decode($dat);
		
		$this->setMessage('Link success, now you can login using Facebook');
		$this->app->user->ext->adminfbid = $dat->id;
		$this->jump();
	}
	
	function doGetWsTempConfig()
	{
		$data = GW_WebSocket_Helper::getFrontConfig($this->app->user, true);
		echo json_encode($data);
		exit;
	}	
	
	
	function doSetI18nExtState()
	{
		$item =& $this->app->user;
		$lns = $item->i18next_lns;
		$ln = $_GET['ln'];
		if(!in_array($ln,GW::s('i18nExt')))
			die('no hacking');
		
		
		$lns[$ln]=(int)$_GET['state'];
		$item->set('ext/i18next_lns',json_encode($lns));
		$this->setMessage(GW::l('/g/ACTION').' '.GW::l('/m/VIEWS/doSetI18nExtState').' <b>'.GW::l("/g/LANG/$ln").'</b> '.($lns[$ln]?'ON':'OFF'), ['float'=>1]);
		
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
}


