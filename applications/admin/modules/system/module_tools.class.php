<?php

include __DIR__.'/module_config.class.php';

class Module_Tools extends Module_Config
{	

	function init()
	{
		parent::init();
	}

	
	function viewDefault()
	{
		list($vars['lastupdates'], $vars['updatefiles']) = $this->__doImportSqlUpdates_list2update();
		
		
		$test_actions = [];
		$test_views = [];
		
		$list = get_class_methods ($this);
		foreach($list as $method){

			if(stripos($method, 'doTest')===0)
				$test_actions[]=[$method, $this->$method];
			
			if(stripos($method, 'viewTest')===0)
				$test_views[]=[substr($method,4), $this->$method];			
		}
				
		$this->tpl_vars['test_actions']=$test_actions;
		$this->tpl_vars['test_views']=$test_views;
				

		return $vars;
	}
	

	function doInstall()
	{
		$this->log[] = GW_Install_Helper::CheckFolders();
	}
	
	function doDebugModeToggle()
	{
		$this->app->sess['debug']=(int)$this->app->sess['debug'];
		$this->app->sess['debug']=($this->app->sess['debug']+1) % 2;
		
		$this->jump();
	}
	
	
	
	
	function __executeQuery($sql)
	{
		
		$sqls = explode(';', $sql);
		
		$db =& $this->app->db;
		
		foreach($sqls as $sql)
		{
			if(!trim($sql))continue;
					
			$db->query($sql, true);
			$aff = $db->affected();

			
			if($db->error)
				$this->app->setErrors($db->error .' Query: '.$db->error_query);
			
			$this->app->setMessage("<pre>".htmlspecialchars($sql).";\n<b># Affected rows:</b> ".$aff."</pre>");
		}
	}	
	
	
	
	function __doImportSqlUpdates_list2update()
	{
		$lastupdates= GW::getInstance('GW_Config')->get('gwcms/last_sql_updates');
		
		
		$list_files = glob(GW::s('DIR/ROOT').'sql/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]-[0-9]*.sql');
		
		$updates = [];
		
		foreach($list_files as $filename)
		{
			if(basename($filename) > $lastupdates)
				$updates[] = $filename;
		}
		
		return [$lastupdates, $updates];
	}
	
	function doImportSqlUpdates()
	{
		list($lastupdates, $updates) = $this->__doImportSqlUpdates_list2update();
		
		foreach($updates as $updatefile)
		{
			$sqls = file_get_contents($updatefile);
			$this->__executeQuery($sqls);
			
			GW::getInstance('GW_Config')->set('gwcms/last_sql_updates', basename($updatefile));
		}
		
		$this->jump();
	}
	
	
	function viewPHPinfo()
	{
		//
		phpinfo();
		
	}
	function viewCompatability()
	{
		$info = PHP_Info_Parser_Helper::parse();
		
		$gd_info=gd_info();
		$apache_loaded_modules=apache_get_modules();
		
		exec('unzip',$unzip);
		
		//satisfies
		$comp = Array
		(
			'phpversion'=>Array('required'=>'5.2.10','current'=>phpversion()),
			'magic_quotes'=>Array('required'=>0, 'current'=>ini_get('magic_quotes_gpc')),
			'short_open_tag'=>Array('required'=>1, 'current'=>ini_get('short_open_tag')),
			'mysql'=>Array('required'=>'5.1','current'=>$this->app->db->fetch_result('SELECT VERSION();')),
			'apache'=>Array('required'=>'2','current'=>$_SERVER['SERVER_SOFTWARE']),
			'gd'=>Array('required'=>'2','current'=>$gd_info['GD Version']),
			'apache_modules'=>Array
			(
				'mod_rewrite'=>Array('required'=>'1', 'current'=>(int)in_array('mod_rewrite', $apache_loaded_modules)),
			),
			'safe_mode'=>Array('current'=>(int)ini_get('safe_mode')),
			'upload_max_filesize'=>Array('current'=>ini_get('upload_max_filesize')),
			'post_max_size'=>Array('current'=>ini_get('post_max_size')),
			'exec unzip'=>Array('current'=> $unzip[0]),
			'admin/cli/system.php process id'=>GW_App_System::getRunningPid()
		);
		
		$this->log[]=$comp;
	}
	

	
	public $doTestBackgroundRequest = ["info"=>"Tests ability to perform scripts with get-conection-close"];
	
	function doTestBackgroundRequest()
	{
		
		$test_string = GW_String_Helper::getRandString(10).' '.date('Y-m-d H:i:s');
		
		$params=[];
		
		/*
		if(isset($_GET['localhost_base']))
			$params['localhost_base']=1;
		
		if(isset($_GET['force_http']))
			$params['force_http']=1;
		 */
		
		$url = Navigator::backgroundRequest($this->buildUri(false,[],['app'=>'admin']), ["act"=>'doATestBackgroundRequest','test_string'=>$test_string]);
		
		sleep(3);
		
		print_r([
			'RequestedUrl'=>$url, 
			'$this->config->backgroundTestValue'=>$this->config->backgroundTestValue,
			'test_string'=>$test_string,
			'passed'=> $test_string == $this->config->backgroundTestValue ? 'yes' : 'no'
		]);
		
	}
	
	
	function doATestBackgroundRequest()
	{
		$this->config->backgroundTestValue = $_GET['test_string'];
		echo "your test string: ".$_GET['test_string'];
		exit;
	}
	
	
	public $doTestEmail = ["info"=>"Check if mail server is correctly configured"];
	
	function doTestEmail()
	{
		$stat = mail($this->app->user->email, "test mail", "test mail body");
		
		$this->app->setMessage("Test mail to: {$this->app->user->email} status ".  var_export($stat, true));
		$this->jump();
	}
	
	
	public $doTestPhpMailer = ['info'=>'Check mail sending with phpmailer class'];
	
	function doTestPhpMailer()
	{
		$from = GW::s('DEFAULT_MAIL_SENDER_ADDR');
		$to = $this->app->user->email;
		
		$mailer = $this->initPhpmailer($from, "testing php mailer");
		$mailer->msgHTML("This is test message body");
		$mailer->addAddress($to);
		
		$status = $mailer->send();
		
		

		$this->app->setMessage("mail send from ".htmlspecialchars($from)." to $to ".($status ? 'succeed':'failed'));
		
		$mailer->clearAllRecipients();
		
		////--------------2nd test----------------------------------
		$mailer->addAddress($to="gwcmsmailtest@mailinator.com");
		$status = $mailer->send();
		
		$this->app->setMessage("2nd mail send from ".htmlspecialchars($from)." to $to ".($status ? 'succeed':'failed'));
		
			
	}
	
	function initPhpmailer($from, $subject, $replyto='')
	{
		$mail = GW::getInstance('phpmailer',GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php');
		//$mail->isSendmail();
		$mail->CharSet = 'UTF-8';
		
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($from);
		$mail->setFrom($email, $name);
		
		if($replyto){
			list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($replyto);
			$mail->addReplyTo($email, $name);
			$mail->__replyTo = $email;
		}
		
		$mail->Subject = $subject;
		
		//$mail->DKIM_domain = $this->config->dkim_domain;
		//$mail->DKIM_private = GW::s('DIR/SYS_FILES').'.mail.key';

		//$mail->DKIM_selector = $this->config->dkim_domain_selector;
		//$mail->DKIM_passphrase = ''; //key is not encrypted
		
		return $mail;
	}	
	
	public $doTestGeoip = ["info"=>"Check if geoip function working (geoip_country_code_by_name)"];
	
	function doTestGeoip()
	{
		if(function_exists('geoip_country_code_by_name')){
			$this->app->setMessage("Feature available. Jour country ".geoip_country_code_by_name($_SERVER['REMOTE_ADDR']));
		}else{
			$this->app->setMessage("Feature not enabled. <a target='_blank' href='http://www.beginninglinux.com/home/php/ubuntu-php-5-geo-ip'>More info</a>");
		}
		
	}
	
	public $viewTestJqueryui = ["info"=>"Test jquery-ui compatability with jquery"];
	
	function viewTestJqueryui()
	{
		
	}

	
	

	
}
