<?php

class Module_NewsLetter extends GW_Public_Module
{

	function init()
	{
		parent::init();
		
		$this->subscriber = new GW_NL_Subscriber;
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
		
		
		$this->config = new GW_Config('newsletter/');
	}
	
	function processView($name, $params = array()) {
		
		$this->app->page->title = isset($this->lang['VIEWS'][$name]) ? $this->lang['VIEWS'][$name] : $this->lang['VIEWS']['default'];
		
		parent::processView($name, $params);
	}
	


	function viewDefault($params)
	{
		//dump($this->lang);
		//exit;		
	}
	
	function __saveHit($data)
	{
			$hit=GW::getInstance('GW_NL_Hit')->createNewObject();
			$hit->setValues(
				$data+[
					'ip'=>$_SERVER['REMOTE_ADDR'],
					'debug'=>$_SERVER['REQUEST_URI'],
					'browser'=>$_SERVER['HTTP_USER_AGENT'],
					'referer'=>isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''
				]
			);
			$hit->insert();			
	}
	
	function __initSubscriber()
	{
		if(!isset($_GET['nlid']) || ! isset($_GET['re']))
			die('Bad request. Code 594654888');
		
		$nlid=$_GET['nlid'];
		$recipient_email=$_GET['re'];
		$recipient_email=  base64_decode($recipient_email);
		
		
		$subscriber = $this->subscriber->find(['email=?', $recipient_email]);
		
		if(!$subscriber)
		{
			$this->app->setMessage("nerasta");
			
			return false;
		}
		
		$this->tpl_vars['newsletter_id']=$nlid;
		$this->tpl_vars['subscriber']=$subscriber;
		$this->tpl_vars['selected_groups']=array_flip($subscriber->groups);
	}
	
	
	function viewSubscribe()
	{		
		$this->__initSubscriber();		
	}
	
	function viewNewSubscribe()
	{
		
	}

	function viewNewSubscribe_Menuturas()
	{
		
	}
	
	
	
	function viewConfirm()
	{
		if(!isset($_GET['rid']) || ! isset($_GET['confirm']))
			die('Bad request. Code 98345243245');
		
		
		if($recipient = $this->subscriber->find(['id=? AND confirm_code=?', $_GET['rid'], $_GET['confirm']])){
			
		
			$recipient->groups = array_keys($this->options['groups']);
			$recipient->active = 1;
			$recipient->confirm_code = 7;
			
			$recipient->save();
			
			$this->tpl_vars['success']=1;
		}else{
			$this->tpl_vars['success']=0;
		}
		
	}
	
	function __sendConfirmMail($subscriber)
	{
		$linkbase = Navigator::getBase(true).'site/';	
		$confirm_link = $linkbase.strtolower($this->app->ln)."/direct/newsletter/newsletter/confirm?rid=".$subscriber->id.'&confirm='.$subscriber->confirm_code;
		$this->smarty->assign('CONFIRM_LINK', $confirm_link);
		
		$body = $this->smarty->fetch('string:'.$this->config->subscribe_confirm_msg);

		if(!$this->config->default_sender)
			die('ERROR NO SENDER CONFIGURED. ERRCODE: 9846a5498g4sf6');		
		
		$mailer = $this->initPhpmailer($this->config->default_sender, $this->config->default_replyto, $this->lang['CONFIRM_SUSBSCRIBE']);
		
		$mailer->addAddress($subscriber->email);
		$mailer->msgHTML($body);
				
		
		if(! $mailer->send()) {	
			die('mail send failed');
		}	
		
		return true;
	}
	
	
	function doNewSubscribe()
	{		
		$subscriber = new GW_NL_Subscriber;
		
		$subscriber->setValues(['email'=>$_REQUEST['email'], 'lang'=>$this->app->ln, 'active'=>0, 'unsubscribed'=>0]);
		$subscriber->setConfirmCode();
		
		if($subscriber->validate())
		{
			$subscriber->insert();
			
			$this->__sendConfirmMail($subscriber);
			
			$response['success']=1;
			
		}else{
			$response['errors'] = $subscriber->errors;
		}
		
		echo $_GET['callback']."(".json_encode($response).");";
		exit;
		
	}
	
	
	
	function viewLink()
	{
		if(!isset($_GET['nlid']) || !isset($_GET['rid']) || !isset($_GET['link']))
			die('Bad link Errcode: 48499444');
		
		$link = base64_decode($_GET['link']);
		
		
		//APTIKTA KLAIDA 2015-07-13
		if(strpos($link,'http')===false)
		{
			$recipient = $this->subscriber->find(['id=? ', $_GET['rid']]);
			$re = base64_encode($recipient->email);
			
			$this->app->jump(dirname($this->app->path).'/item',['nlid'=>$_GET['nlid'],'rid'=>$_GET['rid'], 're'=>$re]);
		}
			
		
		if($nl = GW::getInstance('GW_NL_Message')->find(['id=?', $_GET['nlid']]))
		{
			$this->__saveHit(['message_id'=>$_GET['nlid'], 'subscriber_id'=>$_GET['rid'], 'link'=>$link]);		
		}
		
		Header('Location: '.$link);
	}
	
	function viewLink2()
	{
		if(!isset($_GET['nlid']) || !isset($_GET['rid']) || !isset($_GET['link']))
			die('Bad link Errcode: 48499444');
		
		$nl = GW::getInstance('GW_NL_Message')->find(['id=?', $_GET['nlid']]);
		$link = GW::getInstance('GW_NL_Link')->find(['letter_id=? AND id=?', $_GET['nlid'], $_GET['link']]);
				
		if($nl && $link)
		{
			$this->__saveHit(['message_id'=>$_GET['nlid'], 'subscriber_id'=>$_GET['rid'], 'link'=>$link->id]);	
			
			Header('Location: '.$link->link);
			exit;
		}
		
		
		die('Bad request. Error Code: 15516gf1jr91');
	}	
	
	function viewSuccess()
	{
		
	}
	
	
	function doSubscribe()
	{		
		$this->__initSubscriber();
		
		$item = $this->tpl_vars['subscriber'];
		
		$success = true;
		
		$prev_groups = $item->groups;
		$post_groups = array_filter($_POST['groups'], 'intval');
		
		if($_POST['unsubscribed']==0)
		{
			$item->groups = $post_groups;
			
		}else{
			/*
			if($_POST['email']!=$item->email)
			{
				$this->app->setErrors("Neteisingai nurodytas el. pašto adresas");
				$success = false;
			}*/
			
			$success=true;
		}
		
		if($success){
			
			$item->unsubscribed = $_POST['unsubscribed'];
			$item->save();			
			
			//register hit
			$this->__saveHit([
			    'message_id'=>$this->tpl_vars['newsletter_id'], 
			    'subscriber_id'=>$item->id, 
			    'link'=>$item->unsubscribed ? 'unsubscribe' : 'newsgroup change '.implode(',', $prev_groups).' > '.implode(',', $post_groups),
			]);
			
			$this->app->jump(dirname($this->app->path).'/success');
		}else{
			$this->app->jump(false, $_GET);
		}
	}
	
	function viewItem()
	{
		
		//d::dumpas(GW::s('LOGS'));
		//file_put_contents(GW::s('ROOT_DIR').'', $data)
		
		
		if(!isset($_GET['nlid']) || !isset($_GET['rid']) || !isset($_GET['re']))
			die('Bad link Errcode: 651956'); 
		
		$letter = GW::getInstance('GW_NL_Message')->find(['active=1 AND id=?', $_GET['nlid']]);
		
		if(!$letter)
			die('Link expired');

		
		$recipient = $this->subscriber->find(['id=? AND email=?', $_GET['rid'], base64_decode($_GET['re'])]);
		$this->smarty->assign('NAME', $recipient->name.' '.$recipient->surname);
		
		
		$us_link="subscribe?nlid={$letter->id}&rid=".base64_encode($recipient->email);
		$us_link='<a href="'.$us_link.'">'.$this->lang['LINK'][strtoupper($recipient->lang)].'</a>';
		
		$this->smarty->assign('UNSUBSCRIBE', $us_link);
		
		
		$message = $letter->getBodyFull('body_prepared');
		
		
		$trackinglink2="link2?nlid={$letter->id}&rid=".$recipient->id.'&link=';
		$this->smarty->assign('TRACKINK_LINK', $trackinglink2);
		
		
		//register hit
		
		$this->__saveHit([
		    'message_id'=>$letter->id, 
		    'subscriber_id'=>$recipient->id, 
		    'link'=>'link-newsletter-online'
		]);	
		
		
		
		echo $this->smarty->fetch('string:'.$message);
		exit;
		
	}	

	
	function initPhpmailer($from, $replyto, $subject)
	{
		$mail = new PHPMailer;
		//$mail->isSendmail();
		$mail->CharSet = 'UTF-8';
		
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($from);
		$mail->setFrom($email, $name);
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($replyto);
		$mail->addReplyTo($email, $name);
		
		$mail->Subject = $subject;
		
		
		$cfg = new GW_Config('newsletter/');
		$mail->DKIM_domain = $cfg->dkim_domain;
		$mail->DKIM_private = GW::s('DIR/SYS_FILES').'.mail.key';

		$mail->DKIM_selector = $this->config->dkim_domain_selector;
		$mail->DKIM_passphrase = ''; //key is not encrypted
		
		return $mail;
	}	
	

	
}
