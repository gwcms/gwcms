<?php

class Module_Users extends GW_Public_Module
{

	function init()
	{
		$this->addRedirRule('/^viewOptions/i',['options','Module_public_options']);
		
		
		
		if(GW::s('PROJECT_NAME') == 'events_ltf_lt'){
			$this->addRedirRule('/^ltf_/i',['options','ltf_common_module']);	
			
		}elseif(GW::s('SPORT')){
			$this->addRedirRule('/^ts_/i',['sportosistemos','ts_common_module']);
		}
		
		parent::init();
		
		$this->model = new GW_Customer;
		
		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		//GW::$devel_debug = true;	
		$this->cfg = new GW_Config('customers/');
		
		//d::ldump($this->app->page);
		
		
	}
	
	function getFieldsConfig()
	{
		$availfields = explode(',',$this->cfg->available_fields);
		$required =  array_flip((array)json_decode($this->cfg->registration_fields_required));
		$optional =  array_flip((array)json_decode($this->cfg->registration_fields_optional));
		
		foreach($required as $key => $x)
			$required[$key]=1;
		
		foreach($optional as $key => $x)
			$optional[$key]=1;

		
		return ['required'=>$required,'optional'=>$optional,'fields'=>array_merge($required,$optional)];
	}
	
	function testMissingInfo()
	{
		$fields = $this->getFieldsConfig();
		
		foreach($fields['required'] as $field => $x)
		{
			
			if(!$this->app->user->get($field)){
				if($this->app->path!='direct/users/users/profile')
					$this->setMessage('<b>'.GW::ln("/m/FIELDS/$field").'</b> '.GW::ln('/m/IS_MISSING'));
				
				return false;
			}
		}
		
		return true;
	}
	
	
	function doCheckProfileMissingInfo()
	{
		//return false;
		
		//gali ivykti jei nera kazkokio failo tada redirectina i index
		if($this->app->user && !in_array($this->app->path, ['direct/users/users/profile', 'direct/users/users/logout']) && !isset($_SESSION['3rdAuthUser']) && !$this->testMissingInfo()){
			
			$this->setMessage(["text"=>GW::ln('/m/PLEASE_COMPLETE_ACCOUNT_DETAILS'),"type"=>GW_MSG_WARN]);
			$this->app->jump('direct/users/users/profile');
		}
			
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
			$this->options['club_long'] = LTF_Clubs::singleton()->getOptions(["approved=1 OR user_id=?", $this->app->user->id]) +
					['-1'=>GW::ln('/g/CANT_FIND_IN_LIST')];

			$this->options['coach'] = LTF_Coaches::singleton()->getOptions(["approved=1 OR user_id=?", $this->app->user->id]) +
					['-1'=>GW::ln('/g/CANT_FIND_IN_LIST')];
			
			//d::dumpas($this->options);
			
		}elseif(GW::s('SPORT') && $this->app->user){
			
			$this->ts_initClubOptions();
			$this->ts_initCoachOptions();
		}
		
		$this->initCountryOpt();
	}
	
	function viewRegister()
	{
		if(isset($_GET['after_auth_nav'])){
			$this->app->sess('after_auth_nav', $_GET['after_auth_nav']);
			$this->app->sess('navigate_after_auth', $_GET['after_auth_nav']);
		}
		
		
		//$this->tpl_vars['breadcrumbs_attach'] = [['title'=>GW::ln('/m/VIEWS/register')]];
		
		$item = (object)$this->getErrorItem('reguser');
			
		$this->smarty->assign('item', $item);
		
		if($vals=$this->getErrorItem('reguser')) 
		{
			$this->tpl_vars['item'] = (object)$vals;
		}
		
		if(isset($_REQUEST['success'])){
			if(isset($_REQUEST['authgw']) && ($_REQUEST['authgw']=='fb' || $_REQUEST['authgw']=='gg')){
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
		

		$doverifymail = true;
		
		$link3rdAuth = $vals['3rdAuthUserlink'] ?? false;
		unset($vals['3rdAuthUserlink']);
		
		$item = $this->model->createNewObject($vals);
		
		$item->setValidators('register');
		$item->username = $item->email;
		
		
		if($link3rdAuth)
		{
			$remoteuser = $_SESSION['3rdAuthUser'];
			$map = ['facebook'=>'fbid','google'=>'ggid'];
			$field = $map[$remoteuser->type];	
			$item->$field = $remoteuser->id;
			
			//jei toks pat kaip fb userio nereikia verifikacijos
			if($item->email == $remoteuser->email)
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
				unset($_SESSION['3rdAuthUser']);
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
	
	
	function doRegister3rdPartyAcc()
	{
		$item = $this->model->createNewObject();
		
		$remoteuser = $_SESSION['3rdAuthUser'];
		$map = ['facebook'=>'fbid','google'=>'ggid'];
		$field = $map[$remoteuser->type];		
		
		$item->setValidators('register_fb');
	
		
		$gendermap = ['male'=>'M','female'=>'F'];
		
		$item->username = $remoteuser->email;
		$item->email = $remoteuser->email;
		$item->name = $remoteuser->name;
		$item->surname = $remoteuser->surname;
		$item->gender = $gendermap[$remoteuser->gender] ?? false;
		$item->$field = $remoteuser->id;
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
						
		if(($user = $this->app->auth->loginPass($username, $pass))  || ($user = $this->otherSystemLogin($username, $pass))) {
			
			$this->app->user = $user;
			
			if(isset($_POST['link3rdAuthUser']) && $_POST['link3rdAuthUser']=='on')
			{
				$remoteuser = $_SESSION['3rdAuthUser'];
				$map = ['facebook'=>'fbid','google'=>'ggid'];
				$field = $map[$remoteuser->type];
				
				$user->saveValues([$field=>$remoteuser->id]);
				
				
				//d::dumpas($_SESSION['3rdAuthUser']);
				
				unset($_SESSION['3rdAuthUser']);
								
				$this->setMessage(GW::ln('/M/USERS/PROFILE_WAS_LINKED_WITH_X_ACCOOUNT',['v'=>['type'=> strtoupper($remoteuser->type)]]));
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
		
		
		unset($_SESSION['3rdAuthUser']);
		session_write_close();
		
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
						
			unset($_SESSION['3rdAuthUser']);
				
		}else{
			$this->app->setError("/M/USERS/INVALID_VERIFY_LINK");
		}
		
		unset($_SESSION['3rdAuthUser']);
		
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
	
	
	function filterPermitFields(&$vals, $permit_fields)
	{
		$vals = array_intersect_key($vals, $permit_fields);		
	}	
	
	
	function notifyAdminUserTransfer($user, $olduser)
	{
		$user_short = 
						
			"Name: ".$user->title."\n".
			"Email: ".$user->email."\n".
			"Phone: ".$user->phone."\n".
			"Birthdate: ".$user->birthdate."\n".
			
			"----------old user------------\n".
			"Name: ".$olduser->title."\n".
			"Email: ".$olduser->email."\n".
			"Phone: ".$olduser->phone."\n".
			"Birthdate: ".$olduser->birthdate."\n".
			
			"----------meta info------------\n".
			"Ip: ".$user->reg_ip."\n".
			"country: ".$user->reg_country."\n".
			"lang: ".$user->use_lang."\n".
			"browser: ".$_SERVER['HTTP_USER_AGENT']."\n".
			"";
		
		$link = GW::s('SITE_URL')."admin/lt/customers/users/".$user->id."/form";
		
		$opts=Array(
			'subject'=>"Perkelti istoriniai duomenys",
			'body'=>$user_short."Redagavimo forma admin sistemoje: ".$link,
			'plain'=>1
		);
					
		GW_Mail_Helper::sendMailAdmin($opts);
	}	
	
	
	function transferHistory($destination,$source)
	{
		$destination->fireEvent('BEFORE_CHANGES');
		
		if($source->lic_id2 > $destination->lic_id2){
			$destination->lic_id2 = $source->lic_id2;
		}
		
		if(!$destination->club){
			$destination->club = $source->club;
		}
		
		$fields = ['points_sngl','points_dbl','points_mx', 'rank_mx','rank_sngl','rank_dbl', 'int_points_sngl','int_points_dbl','int_points_mx','int_number'];
		
		foreach($fields as $field)
			$destination->$field = $source->$field;
		
		
		
		
		//if($answers['remove_source']){
			$source->fireEvent('BEFORE_CHANGES');
			$source->active=1;
			$source->removed=1;
			$source->description .= ($source->description ? "\n" :'').date('Y-m-d H:i').' export to #'.$destination->id.' - '.$destination->title;
			
		//}
		
		$destination->description .= ($destination->description ? "\n" :'').date('Y-m-d H:i').' import from #'.$source->id.' - '.$source->title;
		$destination->old_id = $source->id;
		$destination->lic_id = $source->lic_id;
		
		
		$source->updateChanged();
		$destination->updateChanged();
		
		
		$new_id = $destination->id;
		$old_id = $source->id;
		
		$parttbl= TS_Participants::singleton()->table;
		$eventstbl = TS_Events::singleton()->table;
		$q=[];
		$q[]="UPDATE `$parttbl` SET participant1=".(int)$new_id." WHERE participant1=".(int)$old_id;
		$q[]="UPDATE `$parttbl` SET participant2=".(int)$new_id." WHERE participant2=".(int)$old_id;
		$q[]="UPDATE `$parttbl` SET user_id=".(int)$new_id." WHERE user_id=".(int)$old_id;
		$q[]="UPDATE `$parttbl` SET payeer_id=".(int)$new_id." WHERE payeer_id=".(int)$old_id;
		
		$membershiptbl = GW_Membership::singleton()->table;
		$q[]="UPDATE `$membershiptbl` SET user_id=".(int)$new_id." WHERE user_id=".(int)$old_id;
		$q[]="UPDATE `ts_applications` SET player_mx=".(int)$new_id." WHERE player_mx=".(int)$old_id;	
		$q[]="UPDATE `ts_applications` SET player_dbl=".(int)$new_id." WHERE player_dbl=".(int)$old_id;	
		
		//$q[]="UPDATE `$parttbl` SET user_id=".(int)$new_id." WHERE user_id=".(int)$old_id;
		$affected = 0;
		foreach($q as $sql){
			GW::db()->query($sql);
			$affected += GW::db()->affected();
		}		
		
		
		
		if($affected)
			$this->setMessage("Participations count: ".$affected);
			
	
	}
	
	function searchOldUser($user)
	{
		$found_historical = GW_Customer::singleton()->findAll(
			['name=? AND surname=? AND birthdate=? AND id < 12745 AND removed=0', 
			    $user->name, $user->surname, $user->birthdate]);
		
		
		if(count($found_historical)==1){
			
			$this->transferHistory($user, $found_historical[0]);
			$this->notifyAdminUserTransfer($user, $found_historical[0]);
			$this->setMessage(GW::ln('/m/HISTORICAL_DATA_ATTACHED'));
			
		}elseif(count($found_historical)>1){
			$found_historical = GW_Customer::singleton()->findAll(
			['name=? AND surname=? AND birthdate=? AND id < 12745 AND removed=0', 
			    $user->name, $user->surname, $user->birthdate], ['key_field'=>'id']);
			
			
			$opts=Array(
				'subject'=>"Perkelti istoriniu duomenu nepavyko yra rasta daugiau nei vienas atitikmuo",
				'body'=>"naujas vartotojas: #{$user->id} $user->title\nRasti atitikmenys ids: ".implode(',', array_keys($found_historical)),
				'plain'=>1
			);

			GW_Mail_Helper::sendMailAdmin($opts);	
			$this->setPlainMessage("/m/AMBIGUOUS_DATA_OR_CALL_ADMIN", GW_MSG_WARN);
		}else{
			$this->setPlainMessage("/m/HISTORY_NO_DATA_OR_CALL_ADMIN", GW_MSG_INFO);
			$user->old_id = $user->old_id-1;
			
		}
		
	}
	
	function doSaveProfile()
	{
		$this->userRequired();
		
		$item = $this->app->user;
		$vals = $_POST['item'];

		
		if(GW::s('SPORT')){
			$vals = $this->ts_SaveClub($vals);
			$vals = $this->ts_SaveCoach($vals);
		}
		
		$fields = $this->getFieldsConfig();
		$permit_fields = $fields['fields'];
				
		$this->filterPermitFields($vals,$permit_fields+['id'=>1]);	
	
		$item->fireEvent('BEFORE_CHANGES');
		
		$item->setValues($vals);
		
		
		
		if(
			GW::s('OLD_USER_DB') &&
			$item->birthdate && $item->name && $item->surname &&
			(isset($item->changed_fields['birthdate']) || isset($item->changed_fields['surname']))  && $item->old_id < 1
		){
			$this->searchOldUser($item);
		}
		//d::dumpas($vals);
		
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
			//$sort = substr($_COOKIE['user_secret'], 0, 10);
			//GW::db()->update('nat_product_history', ['auser_id = ?',$sort], ['user_id'=>$this->app->user->id]);
		}
		
		if(GW::s('SITE/HOOKS/AFTER_LOGIN')){
			foreach(GW::s('SITE/HOOKS/AFTER_LOGIN') as $path){	
				$this->app->subProcessPath($path);
			}
		}
		
		
		if(isset($_GET['redirect_url'])){
			$redir_url=$_GET['redirect_url']??'';
			
			$url = Navigator::getBase(). ltrim($redir_url,'/');


			Header('Location: '.$url);			
		}
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
	
	
	function otherSystemLogin($username, $pass)
	{
	
		//vartotojas negali egzistuoti sioje sistemoje
		if(GW_User::singleton()->count(['username=?', $username]))
			return false;
		
		//$url = 'http://user:pass@adb/service/user';
		$url = GW_Config::singleton()->get('gw_users/secondcms_userservice_endpoint');
		
		
		$rpc = new GW_General_RPC($url);
		$req = ['user'=>$username,'pass'=>$pass, 'ip'=>$_SERVER['REMOTE_ADDR'], 'user_agent'=>$_SERVER['HTTP_USER_AGENT']];

		$rpc->debug=1;
		//$rpc->basicAuthSetUserPass('aaa','bbb');
		
		$resp = $rpc->call('login', [], $req);
		
		if($resp->user && $resp->user->id){
			$remote_user =  $resp->user;
			
			$cust = GW_Customer::singleton()->createNewObject();
			$secondsys_name = parse_url($url);
			$secondsys_name = $secondsys_name['host'];



			$copyfields = $cust->getFieldTypes();
			
			foreach(['id','insert_time','update_time','login_count'] as $nocopy)
				unset($copyfields[$nocopy]);

			//$copyfields = array_keys($copyfields);
			
			$copy = array_intersect_key((array)$remote_user, $copyfields);
			
			$cust->setValues($copy);
			$cust->description = "Copy from remote syst ($secondsys_name). original user id: {$remote_user->id}";
			if($remote_user->description ?? false)
				$cust->description.=" || Orig descript: ".$remote_user->description;
			
			$cust->pass = $pass;
			$cust->cryptPassword();
			
			$cust->prepareSave();
		
			$cust->setValidators('import');
			$cust->license = 1;
			
			if(!$cust->validate())
			{
				$this->setError("User found in remote system ($secondsys_name) but facing user validation problems");
				$this->setItemErrors($cust);
				$this->setError('Please sign up regular way. Cant import');
				
			
				$info = ['orig_user'=>$remote_user,'copy_fields'=>$copy,'errors'=>$cust->errors];
				$info = json_encode($info, JSON_PRETTY_PRINT || JSON_UNESCAPED_SLASHES || JSON_UNESCAPED_UNICODE);
				$opts = ['subject'=>'User found in remote sys but cant create', 'body'=>$info];
				GW_Mail_Helper::sendMailAdmin($opts);
			}else{
				
				$cust->insert();
				
				
				$this->setMessage("User was imported from other system - <b>$secondsys_name</b>. Keep in mind that data is duplicated so if you change password or other details on one system it wont change on other, if there is need please repeat update on second system in that case");
				
				
				$user = $this->app->auth->loginPass($username, $pass);
				
				return $user;
			}
		}
	}	
	
	
	function viewSignInOrRegister() 
	{

		$remoteuser = $_SESSION['3rdAuthUser'];
		
		

		if(! $remoteuser->id)
		{
			$this->setError("/M/USERS/LOGIN_FAIL");
			$this->app->jump('/');
		}
		
		
		$map = ['facebook'=>'fbid','google'=>'ggid'];
		$field = $map[$remoteuser->type];


		if($user=GW_Customer::singleton()->find(["($field=? OR email=? OR username=?) AND active=1", $remoteuser->id, $remoteuser->email, $remoteuser->email])) 
		{
			//$user->fbid!=$fbusr->id dadejau salyga kad atnaujintu po to kai pasidare appso pasikeitimas
			
			if(!$user->$field || $user->$field!=$remoteuser->id)
				$user->saveValues([$field=>$remoteuser->id]);

				$this->app->user = $user;
				$this->app->auth->login($user);

				$name = $user->name;
				if($this->app->ln=='lt')
					$name = GW_Linksniai_Helper::getLinksnis($name);
				
				$this->app->setMessage(GW::ln("/m/USERS/LOGIN_WELCOME",['v'=>['NAME'=>$name]]));
				
				
				$this->app->subProcessPath('users/users/noview',['act'=>'doAfterLogin']);
				
				//kad nesilinkintu paskiau
				unset($_SESSION['3rdAuthUser']);
				session_write_close();
				
				
				if($this->app->sess('after_auth_nav')){
					$uri = $this->app->sess('after_auth_nav');
					$this->app->sess('after_auth_nav', "");
					header("Location: ".$uri);
					exit;				
				}				
				
				
				$this->app->jump('/');
				
				
				
				
		}
		
		if(!$remoteuser->email)
		{
			$authgw=strtoupper($remoteuser->type);
			$this->app->setMessage("$authgw did not gave email address so, please register with email address, $authgw fast login will be enabled");
			$this->setErrorItem((array)$remoteuser,'reguser');	
			$this->app->jump('direct/users/users/register');
		}		
		
	
		$this->tpl_vars['remoteuser'] = $remoteuser;
	}	
	
	function doSignupOrLink()
	{
		$remoteuser = $_SESSION['3rdAuthUser'];
		$map = ['facebook'=>'fbid','google'=>'ggid'];
		$field = $map[$remoteuser->type];	

		
		if($_POST['action']=='link'){
			
			if($this->app->user->id && !$this->app->user->fbid)
			{
				$this->app->user->saveValues([$field=>$fbusr->id]);

				unset($_SESSION['3rdAuthUser']);
				$this->setMessage(GW::ln('/M/USERS/PROFILE_WAS_LINKED_WITH_X_ACCOOUNT',['v'=>['type'=> strtoupper($remoteuser->type)]]));
				session_write_close();

				$this->app->jump('direct/users/users/login');
			}else{			
				$this->app->jump('direct/users/users/login');
			}
			
		}elseif($_POST['action']=='register'){
			
			$this->app->jump('direct/users/users/register',['act'=>'doRegister3rdPartyAcc']);
			
		}elseif($_POST['action']=='register_custom'){
			
			$this->setErrorItem((array)$remoteuser,'reguser');	
			$this->app->jump('direct/users/users/register');
		}
		
	}	
		
}
