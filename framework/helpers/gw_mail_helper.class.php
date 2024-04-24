<?php

/**
 * Description of gw_mail_helper
 *
 * @author wdm
 */

class GW_Mail_Helper 
{
	static $debug_smtp = false;
	static $last_from = "";
	static $insert_to_queue_if_fail = true;
	static $cfg_cache = false;
	static $secure_smarty = false;
	
	static function loadCfg()
	{
		if(!self::$cfg_cache){
			$cfg = new GW_Config('emails/');
			$cfg->preload('mail_');	
			self::$cfg_cache = $cfg;	
		}
				
		return self::$cfg_cache;
	}
	
	static function initPhpmailer($from='', $cfg=false)
	{
		if(version_compare(PHP_VERSION, '7.4.0') >= 0){
			include_once GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php';

			$mail = new PHPMailer;
		}else if(version_compare(PHP_VERSION, '7.3.0') >= 0){
			//bulksms project runing on 7.3
			include_once GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php';

			//$mail = new PHPMailer;
			$mail = new PHPMailer\PHPMailer\PHPMailer;
		}else{
			$mail = GW::getInstance('phpmailer',GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php');
		}
			
		$mail->ClearAllRecipients( );
		$mail->clearAttachments();//jei atskiram useriui atskiras attach
		$mail->XMailer = "GWCMS v".GW::s('GW_CMS_VERSION').' author Vidmantas Norkus';

		$mail->CharSet = 'UTF-8';
			
		if(!$cfg)
			$cfg = self::loadCfg();
				
		if(!$from)
			$from = $cfg->mail_from;
		
		self::$last_from = $from;
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($from);
		
		$mail->setFrom($email, $name);		
		
		
		
		if($cfg->mail_is_smtp==1){
			$mail->isSMTP();
			
			if(self::$debug_smtp)
				$mail->SMTPDebug = 2;
			
			$mail->Host = $cfg->mail_smtp_host;
			$mail->Port = $cfg->mail_smtp_port;
			$mail->SMTPAuth = true;
			$mail->Username = $cfg->mail_smtp_user;
			$mail->Password = $cfg->mail_smtp_pass;	
			
			if(strpos($cfg->mail_smtp_user, '@gmail.com')!==false){
				$mail->SMTPSecure = "ssl";
			}
		}
		
		//$mail->Subject = $subject;
		//self::initSafeSmarty(); 
		
		return $mail;
	}
	
	static function explodeMultipleEmails($str)
	{
		$arr = explode(';', $str);
		$arr = array_map('trim', $arr);
		return $arr;
	}
	static function implodeMultipleEmails($arr)
	{
		return implode(';', $arr);
	}	
	
	static function initSafeSmarty()
	{	
		
		if(GW::s('SMARTY_VERSION')<4)
			d::dumpas("Please upgrade smarty to version 4");
		
		if(self::$secure_smarty)
			return self::$secure_smarty;
		
		if(isset($_GET['test']))
		{
			d::ldump('SafeSmarty');
		}
		
		$s = new Smarty;

		//error_reporting(0);
		$s->setErrorReporting(0);
		$s->muteUndefinedOrNullWarnings();	
				
		//ant productiono del performanco galima butu netikrinti, po ikelimo istrinti
		$s->compile_check = true;
		$s->error_reporting = GW::s('SMARTY_ERROR_LEVEL8');;
		//$s->security_policy = false;
		$s->force_compile = true;
		//$compiler->known_modifier_type

		$s->security_policy =  new class($s) extends Smarty_Security{
			public function isTrustedPhpFunction($function_name, $compiler){ 
				
				
				if(in_array($function_name, ['number_format', 'str_replace','count'])) 
					return true; 
				
				if(GW::$context->app->user && GW::$context->app->user->isRoot()){
					if(in_array($function_name, ['var_dump'])) return true; 
				}
				
				
				return false;

				//to debug use this line
				//d::ldump($function_name);return true; 

			} 
			public function isTrustedResourceDir($filepath, $isConfig = null){ return true; } 
			public function isTrustedTag($tag_name, $compiler){ 
				//private_print_expression example: $user->title
				if(in_array($tag_name, ['assign','private_modifier','private_print_expression','if','else','ifclose','elseif','foreach','foreachclose'])) return true; 
				
				//assign $x=123;
				
				return false;

			} 
			public function isTrustedStaticClassAccess($class_name, $params, $compiler){ 
				//vertimai ir tt
				//reiktu galimybes ideti prie projekto
				if(in_array($class_name, ['GW','FH','GW_Sum_To_Text_Helper','Adb_Event_Helper'])) return true; 
				
				return false; 
			} 
			public function isTrustedPhpModifier($modifier_name, $compiler){ return false; } 
			public function isTrustedConstant($const, $compiler){ return false; } 
			public function isTrustedModifier($modifier_name, $compiler){ return false; } 
			public function isTrustedSpecialSmartyVar($var_name, $compiler){ return false; } 
		};
			
		$s->compile_dir = GW::s("DIR/TEMPLATES_C");
		$s->template_dir = false;


		$s->_file_perms = 0666;
		$s->_dir_perms = 0777;
			
		self::$secure_smarty = $s;
			
		return $s;
	}
	
	static function prepareSmartyCode($tpl_code, &$vars)
	{		
		//$s = GW::$context->app->smarty; // unsafe version
		$s = self::initSafeSmarty();
		$s->assign($vars);
		
		
		return $s->fetch('string:' . $tpl_code);
		
	}
	
	
	static function __fSubjBody($body_tru_subj_fal, &$opts, $tpl, $ln, &$vars)
	{
		$what = $body_tru_subj_fal ? 'body':'subject';
		$source = $tpl->get("{$what}", $ln);
		
		if(!isset($opts[$what]))
		{
			if($tpl->format_texts == 2){
				$opts[$what] = self::prepareSmartyCode($source, $vars);
			}elseif($tpl->format_texts == 0){
				$opts[$what] = $source;
			}
		}		
	}
	
	static function initGenericVars(&$vars)
	{
		$vars['SITE_DOMAIN'] = parse_url(GW::s('SITE_URL'), PHP_URL_HOST);		
	}
	
	static function processTpl(&$opts)
	{
		$tpl = $opts['tpl'];
		
		
		//paduodamas sablonas arba sablono id
		//betkokiu atveju $tpl pavirsta i GW_Mail_Template objekta
		if(is_numeric($tpl))
			$tpl = GW_Mail_Template::singleton()->find($tpl);
			
		
		$vars =& $opts['vars'] ?? [];
		self::initGenericVars($vars);
		
		$ln = $opts['ln'] ?? GW::$context->app->ln;
		
		self::__fSubjBody(true, $opts, $tpl, $ln, $vars);
		self::__fSubjBody(false, $opts, $tpl, $ln, $vars);
		
		if(!isset($opts['from']) && $tpl->custom_sender)
			$opts['from'] = $tpl->get("sender", $ln);
		
		if($tpl->bcc){
			$bcc = explode(';', $tpl->bcc);
			$opts['bcc'] = $opts['bcc'] ? array_merge((array)$opts['bcc'], $bcc) : $bcc;
		}
		
	}
	
	static function sendMail(&$opts)
	{
		if($opts instanceof GW_Mail_Queue){
			$m_queue_item = $opts;
			$opts = $m_queue_item->toArray();
		}
		
		$splitAddr = function(&$to, &$name){
			if(strpos($to,'<')!==false){
				list($name, $to) = GW_Email_Validator::separateDisplayNameEmail($to);	
			}else{
				$name="";
			}
		};
		
		$cfg = self::loadCfg();
		$toname = '';
		
		if(isset($opts['tpl']))
			self::processTpl($opts);
		
		$mailer = $opts['mailer'] ?? self::initPhpmailer($opts['from'] ?? '', $opts['cfg'] ?? false);
				
		if(isset($opts['subject']))
			$mailer->Subject = $opts['subject'];
		
		if(isset($opts['plain']) && $opts['plain']){
			$mailer->Body = $opts['body'];
		}else{
			$mailer->msgHTML($opts['body']);
		}

		if(!is_array($opts['to']))
			$opts['to'] = self::explodeMultipleEmails($opts['to']);
				
		foreach($opts['to'] as $to){
			$splitAddr($to, $toname);
			$mailer->addAddress($to, $toname);
		}
		
		if(isset($opts['cc'])){
			$to = $opts['cc'];
			$splitAddr($to, $toname);
			$mailer->addCC($to, $toname);
		}
		
		if(isset($opts['replyto'])){
			$to = $opts['replyto'];
			$splitAddr($to, $toname);
			$mailer->addReplyTo($to, $toname);
		}
		
		
		if(isset($opts['attachments']) && is_array($opts['attachments'])){
			foreach($opts['attachments'] as $filename => $data)
				$mailer->addStringAttachment($data, $filename);
		}
		

		if(isset($opts['bcc'])){
			if(!is_array($opts['bcc']))
				$opts['bcc'] = [$opts['bcc']];
				
			foreach($opts['bcc'] as $bcc)
				$mailer->addBCC($bcc);
		}
		
		if($cfg->mail_bcc_all && !isset($opts['noAdminCopy'])){
			$mailer->addBCC($cfg->mail_bcc_all);
		}
				
		if(isset($opts['dryrun'])){
			return false;
		}
		
		if(isset($opts['debug']))
			d::dumpas([$opts, $mailer]);
		
		if(isset($opts['preview']))
			return $opts;
		
		
		
		
		if(isset($opts['draft']) && !isset($m_queue_item))
		{
			$opts['status'] = 'draft';
			self::add2db($opts);
			return true;
		}
		
		if(isset($opts['scheduled']) && !($m_queue_item ?? false))
		{
			$opts['status'] = 'scheduled';
			self::add2db($opts);
			return true;
		}
		
		
			
		
		try {
			$mailer->send();	
			
			if(!$mailer->isError()){
				$opts['status']="SENT";
				$status = true;
			}else{
				$opts['status'] = $mailer->ErrorInfo ? $mailer->ErrorInfo : 'unknown error';
				$status = false;
			}
			
		} catch (phpmailerException $e) {
			$opts['error'] = $e->errorMessage();
		} catch (Exception $e) {
			$opts['error'] = $e->getMessage();
		}
		
		//saugoti tuo atveju jei yra sukonfiguruota kad saugoti errorus ir yra erroras
		//arba jei neeroras bet sukonfiguruota adminkej kad saugoti visus
		//nesaugoti jei paduodamas parametras nostoredb
		if(((!$status && self::$insert_to_queue_if_fail) || $cfg->mail_insert_succ==1) && !isset($opts['noStoreDB'])){
			
			self::add2db($opts, $m_queue_item ?? false);
			
			//because &$opts not $opts
		}
		
		/*
		 * tai jau atliekama emails/email_queue modulyje
		if($m_queue_item ?? false){
			$m_queue_item->setValues(['status'=> $opts['status']]);
		}
		*/
		
		//d::dumpas($mailer);
		
		$mailer->ClearAllRecipients( );
		$mailer->clearAttachments();//jei atskiram useriui atskiras attach		
		
		
		return $status;
	}
	
	static function sendSms($opts)
	{
		$path="datasources/sms";

		self::processTpl($opts);
		//d::dumpas($opts);
		
		$req = ["number"=>$opts['to'], 'msg'=>$opts['body'], "act"=>'doInsertNew', "json"=>1,'packets'=>1];
		if($opts['scheduled'] ?? false)
			$req['send_time'] = $opts['scheduled'];
		//d::dumpas($req);
		
		
		

		$token = GW_Temp_Access::singleton()->getToken(GW_USER_SYSTEM_ID, '10 minute', $path);
		$req['temp_access'] = GW_USER_SYSTEM_ID . ',' . $token;					


		$respo = GW::$context->app->innerRequest("datasources/sms", $req);		
		//d::dumpas($respo);
		return $respo;
	}	
	
	static function add2db(&$opts, $m_queue_item=false)
	{
		$vals=[];
		GW_Array_Helper::copy($opts, $vals, ['id','body','subject','from','to','plain','error','scheduled','status']);

		
		if($m_queue_item){
			$m_queue_item->setValues($vals);
			$m_queue_item->update();
			$opts=$m_queue_item;
		}else{
			$entry = GW_Mail_Queue::singleton()->createNewObject($vals);
			//jei queued tai galimai saugos antra kart poto statusa uzfiksuot
			$entry->save();
			$opts['id'] = $entry->id;
		}		
	}
	
	
	static function getAdminAddr()
	{
		$cfg = self::loadCfg();
		
		return $cfg->mail_admin_emails;
	}
	
	static function getDeveloperAddr()
	{
		return GW_User::singleton()->find('id=9')->email;
	}
		
	/**
	 * 
	 * $opts - body, subject, to - nustatomas admino meilas, argumentai per linka paduodami tai galima suzinoti koks admino meilas
	 * 
	 */
	static function sendMailAdmin(&$opts)
	{		
		$opts['to'] = self::getAdminAddr();
		
		return self::sendMail($opts);
	}
	
	static function sendMailDeveloper(&$opts)
	{		
		$opts['to'] = self::getDeveloperAddr();
		
		return self::sendMail($opts);
	}	
	
	static function setAdminStatusMSG($controler, $status, $opts)
	{
		$opts['to']=implode(',', $opts['to']);
		
		$controler->setMessage([
			"text" => "Mail send from ".htmlspecialchars(GW_Mail_Helper::$last_from)." to {$opts['to']} ".($status ? 'succeed':'failed'),
			'type' => $status ? GW_MSG_SUCC : GW_MSG_ERR,
			'footer' => $opts['error'] ?? '',
			'float'=>1
		]);			
	}
	
}
