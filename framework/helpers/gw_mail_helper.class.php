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
	
	static function loadCfg()
	{
		if(!self::$cfg_cache){
			$cfg = new GW_Config('emails/');
			$cfg->preload('mail_');	
			self::$cfg_cache = $cfg;	
		}
				
		return self::$cfg_cache;
	}
	
	static function initPhpmailer($from='')
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
		}
		
		//$mail->Subject = $subject;
		
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
	
	
	static function prepareSmartyCode($tpl_code, &$vars)
	{
		GW::$context->app->smarty->assign($vars);
		return GW::$context->app->smarty->fetch('string:' . $tpl_code);
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
	
	static function processTpl(&$opts)
	{
		$tpl = $opts['tpl'];
		
		
		//paduodamas sablonas arba sablono id
		//betkokiu atveju $tpl pavirsta i GW_Mail_Template objekta
		if(is_numeric($tpl))
			$tpl = GW_Mail_Template::singleton()->find($tpl);
			
		
		$vars =& $opts['vars'] ?? [];
		$ln = $opts['ln'] ?? GW::$context->app->ln;
		
		self::__fSubjBody(true, $opts, $tpl, $ln, $vars);
		self::__fSubjBody(false, $opts, $tpl, $ln, $vars);
		
		if(!isset($opts['from']) && $tpl->custom_sender)
			$opts['from'] = $tpl->get("sender", $ln);
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
		
		$mailer = $opts['mailer'] ?? self::initPhpmailer(isset($opts['from'])? $opts['from']:'');
				
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
				
		if(isset($opts['debug']))
			d::dumpas($mailer);
		
		if(isset($opts['preview']))
			return $opts;
		
		
		if(isset($opts['scheduled']) && !$m_queue_item)
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
	
	static function add2db($opts, $m_queue_item=false)
	{
		$vals=[];
		GW_Array_Helper::copy($opts, $vals, ['id','body','subject','from','to','plain','error','scheduled','status']);


		if($m_queue_item){
			$m_queue_item->setValues($vals);
			$m_queue_item->update();
			$opts=$m_queue_item;
		}else{
			GW_Mail_Queue::singleton()->createNewObject($vals)->insert();
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
