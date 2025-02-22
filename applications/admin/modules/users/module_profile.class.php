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

	function doStoreSubscription()
	{
		$subscription = json_decode($_POST['data'], true);
		

		if (!isset($subscription['endpoint'])) {
			echo 'Error: not a subscription';
			return;
		}


		$file = GW::s('DIR/LOGS').'subscriber.dat';
		
		
		if(isset($_REQUEST['unsubscribe'])){

			//cia reiketu issaugoti ir statusa kad auto notificationu nenori
			
			file_put_contents($file, "");
			echo "Subscription cleared";
		}else{
			
			$subscription['user_agent'] = $_POST['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'];
			$subscription['insert_time'] = date('Y-m-d H:i');
			
			file_put_contents($file, json_encode($subscription));
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
	
	function doTestNotification()
	{
		$file = GW::s('DIR/LOGS').'subscriber.dat';
		$subscriber = json_decode(file_get_contents($file), true);
		
		
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


