<?php

class Module_Users extends GW_Public_Module
{

	function init()
	{
		$this->addRedirRule('/^viewOptions/i',['options','Module_public_options']);
		
		
		
		if(GW::s('PROJECT_NAME') == 'events_ltf_lt'){
			$this->addRedirRule('/^ltf_/i',['options','ltf_common_module']);
		}
		
		parent::init();
		
		$this->model = new GW_Customer;
		
		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		//GW::$devel_debug = true;	
		$this->cfg = new GW_Config('customers/');
		
		//d::ldump($this->app->page);
		
		
	}

	
	function viewDefault()
	{
		if(!$this->app->user)
			$this->app->jump('direct/users/users/login');
	}
	

	function viewLogin($params)
	{
		if(isset($_GET['after_auth_nav'])){
			$this->app->sess('after_auth_nav', $_GET['after_auth_nav']);
		}		
		
		//$this->tpl_vars['breadcrumbs_attach'] = [['title'=>GW::ln('/m/VIEWS/login')]];
		
		$this->tpl_vars['login']=(object)[
		    'username'=>isset($_COOKIE['login_0']) ? $_COOKIE['login_0'] : false,
		    'auto'=>isset($_GET['auto']) ? $_GET['auto'] : false,
		];
	}
	
	function initCountryOpt()
	{
		$opts = GW_Country::singleton()->getOptions($this->app->ln);	
		
		$prio = [];
		$prio['LT'] = $opts['LT'];
		$prio['LV'] = $opts['LV'];
		$prio['PL'] = $opts['PL'];
		unset($opts['LT']);
		unset($opts['LV']);
		unset($opts['PL']);
		$opts = $prio + $opts;
		
		$this->tpl_vars['countries'] =  $opts;
		
	}
	
	function initOptions()
	{
		
		
		if(GW::s('PROJECT_NAME') == 'events_ltf_lt' && $this->app->user){
			$this->options['club'] = LTF_Clubs::singleton()->getOptions(["approved=1 OR user_id=?", $this->app->user->id]) +
					['-1'=>GW::ln('/g/CANT_FIND_IN_LIST')];

			$this->options['coach'] = LTF_Coaches::singleton()->getOptions(["approved=1 OR user_id=?", $this->app->user->id]) +
					['-1'=>GW::ln('/g/CANT_FIND_IN_LIST')];
		}
		
		$this->initCountryOpt();
	}
	
	function viewRegister()
	{
		if(isset($_GET['after_auth_nav'])){
			$this->app->sess('after_auth_nav', $_GET['after_auth_nav']);
		}		
		
		
		//$this->tpl_vars['breadcrumbs_attach'] = [['title'=>GW::ln('/m/VIEWS/register')]];
		
		$item = (object)$this->getErrorItem('reguser');
			
		$this->smarty->assign('item', $item);
		
		if($vals=$this->getErrorItem('reguser')) 
		{
			$this->tpl_vars['item'] = (object)$vals;
		}
		
		if(isset($_REQUEST['success'])){
			if(isset($_REQUEST['authgw']) && $_REQUEST['authgw']=='fb'){
				$msg = GW::ln('/M/users/USER_REGISTER_SUCCESS');
			}else{
				$msg = GW::ln('/m/REGISTER_SUCCESS_CONFIRM_EMAIL');
				GW_String_Helper::replaceVarsInTpl($msg, ['EMAIL'=>$_SESSION['email_registered']]);
			}
			$this->tpl_vars['success_message'] = $msg;		
		}	
		
		
		$this->tpl_vars['recapPublicKey'] = GW_Config::singleton()->get('support/recapPublicKey');
		
		$this->initOptions();
	}
	
	function verifyRecaptchaV2($secretKey)
	{
		//https://www.google.com/u/3/recaptcha/admin/site/437873903
		
		if(isset($_POST['g-recaptcha-response'])){
			$captcha=$_POST['g-recaptcha-response'];
		}
		
		if(!$captcha){
			return [];
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		// post request to server
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
		$response = file_get_contents($url);
		$arrResponse = json_decode($response,true);
	      // should return JSON with success as true
		if($arrResponse["success"]) {
			$arrResponse['pass']=1;
		} else {
			$arrResponse['pass']=1;
		}
		
		return $arrResponse;
	}		
		
	
	
	function notifyAdminNewUser($user)
	{
		$user_short = 
			"Name: ".$user->title."\n".
			"Email: ".$user->email."\n".
			"Registration Ip: ".$user->reg_ip."\n".
			"Registration country: ".$user->reg_country."\n".
			"Site lang: ".$user->use_lang."\n".
			"";
		
		$link = GW::s('SITE_URL')."admin/lt/customers/users/".$user->id."/form";
		
		$opts=Array(
			'subject'=>"Sukurtas naujas vartotojas",
			'body'=>$user_short."Redagavimo forma admin sistemoje: ".$link,
			'plain'=>1
		);
					
		GW_Mail_Helper::sendMailAdmin($opts);
	}
	

	
	function doRegister()
	{
		$vals = $ovals = $_POST['item'];
		
		$linkfb = $vals['link_with_fb'] ?? false;
		unset($vals['link_with_fb']);
		$doverifymail = true;
		
		$item = $this->model->createNewObject($vals);
		
		$item->setValidators('register');
		$item->username = $item->email;
		
		if($linkfb)
		{
			$fbusr = $_SESSION['fb_user'];
			$item->fbid = $fbusr->id;
			
			//jei toks pat kaip fb userio nereikia verifikacijos
			if($item->email == $fbusr->email)
				$doverifymail = false;
		}
		
		
		$this->__doRegisterPrepareUser($item);
		$item->prepareSave();
		
		if(!$this->verifyRecaptchaV2(GW_Config::singleton()->get('support/recapPrivateKey'))){
			$item->errors['recaptcha'] = GW::ln('/G/validation/RECAPTCHA_FAILED');
		}	
		
		if($item->validate())
		{					
			if($doverifymail){
				$item->active=0;
				$item->insert();
				$this->sendVerificationEmail($item);
				
				//per daug spamo
				//if($this->identifyNotRobot($item)){
				//	$this->notifyAdminNewUser($item);
				//}
				
				$_SESSION['email_registered'] = $item->email;
				
				$this->setMessage(GW::ln('/M/users/REGISTER_NEED_VERIFY', ['v'=>['EMAIL'=>$item->email]]));
				
				$this->app->jump('/');
								
			}else{
				$item->active=1;
				$item->insert();
				$this->notifyAdminNewUser($item);
				
				$this->app->auth->login($item);
				
				
				$this->setMessage(GW::ln('/M/users/USER_REGISTER_SUCCESS', ['v'=>['EMAIL'=>$item->email]]));
				
				
				
				$this->app->jump('/');
			}
		}else{
			$this->setItemErrors($item);
			$this->setErrorItem($ovals,'reguser');				
		}
		
		$this->app->jump();
	}
	
	function __doRegisterPrepareUser($user)
	{
		$user->reg_ip = $_SERVER['REMOTE_ADDR'];

		if(function_exists('geoip_country_code_by_name')){
			//http://www.beginninglinux.com/home/php/ubuntu-php-5-geo-ip
			$user->reg_country = geoip_country_code_by_name($user->reg_ip);
		}
		
		$user->use_lang = $this->app->ln;
	}	
	
	
	function doRegisterFBacc()
	{
		$item = $this->model->createNewObject();
		
		$item->setValidators('register_fb');
	
		$fbusr = $_SESSION['fb_user'];
		$gendermap = ['male'=>'M','female'=>'F'];
		
		$item->username = $fbusr->email;
		$item->email = $fbusr->email;
		$item->name = $fbusr->name;
		$item->surname = $fbusr->surname;
		$item->gender = $gendermap[$fbusr->gender] ?? false;
		$item->fbid = $fbusr->id;
		$item->active=1;
		
		$this->__doRegisterPrepareUser($item);
		$item->prepareSave();
		
		
		if($item->validate())
		{		
			$item->insert();	
			
			$this->app->auth->login($item);
			
			$this->setMessage('/M/users/USER_REGISTER_SUCCESS');
			$this->app->jump('/');
		}else{
			$this->setItemErrors($item);
			$this->setErrorItem($item->toArray(), 'reguser');
			
			$this->app->jump();
		}
	}
	
	
	function identifyNotRobot($user)
	{
		if(in_array($user->reg_country, ['BY','UA']) && $user->use_lang == 'lt'){
			$user->info = ["robot"=>'possible'];
			return false;
		}else{
			return true;
		}
	}
	
	
	function doLogin()
	{
		$login = $_POST['login'];
		
		list($username, $pass) = $login;
		setcookie('login_0', $username, strtotime('+3 MONTH'), $this->app->sys_base);
						
		if($user = $this->app->auth->loginPass($username, $pass)) {
			
			$this->app->user = $user;
			
			if(isset($_POST['linkfb']) && $_POST['linkfb']=='on')
			{
				$user->saveValues(['fbid'=>$_SESSION['user_link_with_fb']->id]);
				
				//d::dumpas($_SESSION['user_link_with_fb']);
				
				unset($_SESSION['user_link_with_fb']);
								
				$this->setMessage('/M/USERS/PROFILE_WAS_LINKED_WITH_FB_ACCOOUNT');
			}
			
			if(isset($_REQUEST['login_auto']) && GW_Auth::isAutologinEnabled())
			{
				setcookie('login_7', $this->app->user->getAutologinPass(), strtotime(GW::s('GW_AUTOLOGIN_EXPIRATION')), $this->app->sys_base);
				$this->app->auth->session['autologin']=1;
			}
			
			
			$this->doAfterLogin();
			
			$this->testIfJumpRequest();
			
			//GW::$app->setMessage("/m/USERS/LOGIN_WELCOME");
			$this->app->jump(isset($_GET['returnto_url']) ? $_GET['returnto_url'] : GW::s('SITE/PATH_USERZONE'));
			
			
		} else {
			$this->setError($this->app->auth->error);
		}
		
		
		$this->app->carry_params['returnto_url']=1;
		
		$this->app->jump(false,[
		    'error'=>1, 
		    'auto'=> isset($_REQUEST['login_0']) ? $_REQUEST['login_0'] : false
		]);
	}
	
	function testIfJumpRequest()
	{
		if($tmp = $this->app->sess('navigate_after_auth')){
			$this->app->sess('navigate_after_auth', null);
			header("Location: ".$tmp);
			exit;
		}	
		
		if($tmp = $this->app->sess('after_auth_nav')){
			$this->app->sess('after_auth_nav', null);
			header("Location: ".$tmp);
			exit;			
		}
	}	
	
	function viewLogout()
	{		
		$this->app->setMessage("/M/USERS/LOGOUT_MESSAGE");
		
		if($this->app->user){
			$this->app->user->onLogout();
			$this->app->auth->logout();
		}
		
		
		setcookie('user_secret', false, time()+3600*24*365*10, Navigator::getBase());	
		
		
		$this->app->jump('/');	
	}
	
	function doPassChange()
	{
		$item = $this->model->find(Array('email=? AND removed=0 AND active=1', $_POST['email']));
		
		if(!$item){
			return $this->setError("/M/USERS/NO_USER_BY_EMAIL");
		}
		
		if($item->site_passchange && !$item->passChangeExpired()){
			return $this->setMessage("/M/USERS/PASSCHANGE_ALLREADY_SENT");
		}
		
		$secret = $item->setPassChangeSecret();
		
		
		$passchange_link = $this->app->buildURI('direct/users/users/passchange',['id1'=>$item->id,'id2'=>$secret],['absolute'=>1]);
			

		$opts = [
		    'to'=>$item->email,
		    'tpl'=>$this->cfg->pass_change_mail_tpl_id,
		    'vars'=>['LINK'=>"<a href='$passchange_link'>$passchange_link</a>"]
		];
		
		GW_Mail_Helper::sendMail($opts);
					
		
		$this->app->setMessage(sprintf(GW::ln("/M/USERS/PASS_CHANGE_LINK_SENT"), $item->email));
		
		
	
		$this->app->jump('direct/users/users/login');
	}
	
	function sendVerificationEmail($item)
	{
		$path = $this->app->path;
		
		
		$secret = $item->setSignUpApprovalSecret();
		$verif_link = $this->app->buildURI('direct/users/users/login',['act'=>'doVerifyAccount','id1'=>$item->id,'id2'=>$secret],['absolute'=>1]);
		
				
		$opts = [
		    'to'=>$item->email,
		    'tpl'=>$this->cfg->verify_mail_tpl_id,
		    'vars'=>['CONFIRM_LINK'=>"<a href='$verif_link'>$verif_link</a>"]
		];
				
		
		GW_Mail_Helper::sendMail($opts);
	}
	
	function doVerifyAccount()
	{
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND site_verif_key=? AND removed=0 AND active=0', $id, $key));
				
		if($item)
		{				
			$this->app->setMessage(GW::ln("/M/USERS/VERIFY_SUCCESS", ['v'=>['EMAIL'=>$item->email]]));
			
			$item->active=1;
			$item->site_verif_key = '';
			$item->updateChanged();
						
			unset($_SESSION['user_link_with_fb']);
				
		}else{
			$this->app->setError("/M/USERS/INVALID_VERIFY_LINK");
		}
		
		unset($_SESSION['user_link_with_fb']);
		
		//$this->setMessage('/M/users/ACCOUNT_REGISTRATION_FINISHED');
		$this->app->jump('direct/users/users/login');
	}
	
	
	function doEmailVerification()
	{
		d::dumpas([$_GET['userid'],$_GET['verification']]);;
	}	
	
	function viewPassChange()
	{				
		if(isset($_GET['id1']) && isset($_GET['id2'])){
			$id = $_GET['id1'];
			$key = $_GET['id2'];

			$item = $this->model->find(Array('id=? AND site_passchange=? AND removed=0 AND active=1', $id, $key));


			if(!$item){
				$this->setError("/M/USERS/INVALID_PASSCHANGE_LINK");

				$this->app->jump('/');
			}
			
			$this->tpl_name = 'passchange';
		
		}else{
			$this->tpl_name = 'passreset';
		}
	}
	
	function viewProfile()
	{
		$this->userRequired();
		
		if($vals = $this->getErrorItem('profile')){
			$this->app->user->setValues($vals);
			
		}
		
		$this->initOptions();
	
		
				
		$this->tpl_vars['item'] = $this->app->user;		
	}
	
	
	function informAdmin($subject, $body)
	{

				
		
	}
	
	function doSaveProfile()
	{
		$this->userRequired();
		
		$item = $this->app->user;
		$vals = $_POST['item'];

		
		if(GW::s('PROJECT_NAME') == 'events_ltf_lt'){
			$vals = $this->ltf_SaveClub($vals);
			$vals = $this->ltf_SaveCoach($vals);
		}
		
				
	
		$item->setValues($vals);
		
		$item->setValidators('profile');
		
		
		
		
				
		
		if($item->validate()){
			$item->updateChanged();
			
			$this->setMessage(GW::ln('/m/PROFILE_WAS_UPDATED'));
			
			
			
			if(($path=$this->app->sess('jump_after_profile_save')) || ($path= ($_GET['return_to'] ?? false) ))
			{
				header("Location: ". $path);
				$this->app->sess('jump_after_profile_save', false);
				exit;
			}else{
				$this->app->jump('/');
			}
		}else{
			$this->setErrorItem($item->content_base, 'profile');
			$this->setItemErrors($item);
			
			$this->app->jump();
		}
	}	
	
	
	function viewPassReset()
	{
		$this->app->jump('direct/users/users/passchange');
	}
	
	function doPassChange2()
	{
		$id = $_GET['id1'];
		$key = $_GET['id2'];
		
		$item = $this->model->find(Array('id=? AND site_passchange=? AND removed=0 AND active=1', $id, $key));
		
		$item->set('pass_new',$_POST['login_id'][0]);
		$item->set('pass_new_repeat',$_POST['login_id'][1]);
		$item->setValidators('change_pass_repeat');
	
		$item->prepareSave();
		//d::dumpas([$item->validate(), $item->content_base]);
		
		if(!$item->validate())
		{
			$this->setItemErrors($item);
		}else{
			$item->site_passchange='';
			$item->update();
			
			$this->app->setMessage('/M/USERS/PASSRESET_PASS_CHANGED');
			$this->app->jump('direct/users/users/login');			
		}
	}
	

	
	function viewSignInUpDialog()
	{
		
	}
	
	
	function viewBirthDateForm()
	{
		if($vals = $this->getErrorItem('dateinput')){
			$this->app->user->setValues($vals);
		}
		
		
		$this->tpl_vars['item'] = $this->app->user;
	}
	
	
	
	
	
	function doSaveBirthDate()
	{
		$this->userRequired();
		$item = $this->app->user;
		$vals = $_POST['item'];
	
		$item->setValues($vals);
		$item->setValidators('birthdate');
		
		if($item->validate()){
			$item->update(['birthdate']);
		}else{
			$this->setErrorItem($item->content_base, 'dateinput');
			$this->setItemErrors($item);
			$this->app->jump();
		}
		
		if($this->app->sess('jump_after_bday'))
		{
			header("Location: ". $this->app->sess('jump_after_bday'));
			$this->app->sess('jump_after_bday', false);
			exit;
		}
	}
	
	
	function doAfterLogin()
	{
		
		if(isset($_COOKIE['user_secret'])){
			$sort = substr($_COOKIE['user_secret'], 0, 10);
			GW::db()->update('nat_product_history', ['auser_id = ?',$sort], ['user_id'=>$this->app->user->id]);
		}
	}
	
	

	
	function doNoteIformCountry()
	{
		$this->setMessage(GW::ln('/g/PLEASE_PROVIDE_COUNTRY'));
	}
	

	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($o){ return $o->title." ".explode('-',$o->birthdate)[0]; },
		    'condition_add'=>"is_admin=0 AND active=1",
		    'search_fields'=>["concat(name,' ',surname, ' ',club)"]
		];	
		
		return $opts;	
	}	
	
	
	function getInputFileItem()
	{
		return $this->app->user;
	}	
	
	function canBeAccessed($item, $opts=[])
	{
		if($item->id != $this->app->user->id){

			$this->setError('/G/GENERAL/ACTION_RESTRICTED');
			$this->jump();
		}else{
			return parent::canBeAccessed($item);
		}
	}	
}
