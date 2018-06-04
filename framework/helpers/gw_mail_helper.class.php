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
			$cfg = new GW_Config('sys/');
			$cfg->preload('mail_');	
			self::$cfg_cache = $cfg;	
		}
		
		return self::$cfg_cache;
	}
	
	static function initPhpmailer($from='', $subject='')
	{
		$mail = GW::getInstance('phpmailer',GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php');
		
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
		
		$mail->Subject = $subject;
		
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
	
	static function sendMail($opts)
	{
		if($opts instanceof GW_Mail_Queue){
			$m_queue_item = $opts;
			$opts = $m_queue_item->toArray();
		}
		
		$cfg = self::loadCfg();
		
		$mailer = self::initPhpmailer(isset($opts['from'])? $opts['from']:'', $opts['subject']);
		
		
		if(isset($opts['plain'])){
			$mailer->Body = $opts['body'];
		}else{
			$mailer->msgHTML($opts['body']);
		}

		
		if(is_array($opts['to'])){
			foreach($opts['to'] as $to)
				$mailer->addAddress($to);
		}else{
			$mailer->addAddress($opts['to']);
		}
		
		if(!isset($opts['noAdminCopy'])){
			$mailer->addBCC($cfg->mail_admin_emails);
			unset($opts['noAdminCopy']);
		}
		
		try {
			$status = $mailer->send();
			$opts['error']="SENT";
		} catch (phpmailerException $e) {
			$opts['error'] = $e->errorMessage();
		} catch (Exception $e) {
			$opts['error'] = $e->getMessage();
		}

		//saugoti tuo atveju jei yra sukonfiguruota kad saugoti errorus ir yra erroras
		//arba jei neeroras bet sukonfiguruota adminkej kad saugoti visus
		//nesaugoti jei paduodamas parametras nostoredb
		if(((!$status && self::$insert_to_queue_if_fail) || $cfg->mail_insert_succ==1) && !isset($opts['noStoreDB'])){
			if(isset($m_queue_item)){
				$m_queue_item->setValues($opts);
				$m_queue_item->update();
			}else{
				GW_Mail_Queue::singleton()->createNewObject($opts)->insert();
			}
		}
		
		
		return $status;
	}
	
		
	function sendMailAdmin($opts)
	{
		$cfg = self::loadCfg();
		
		$opts['to'] = self::explodeMultipleEmails($cfg->mail_admin_emails);
		
		return self::sendMail($opts);
	}

}
