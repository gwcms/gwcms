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


	function viewDefault($params)
	{
		//dump($this->lang);
		//exit;		
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
	
	function viewConfirm()
	{
		if(!isset($_GET['rid']) || ! isset($_GET['confirm']))
			die('Bad request. Code 98345243245');
		
		
		if($recipient = $this->subscriber->find(['id=? AND confirm_code=?', $_GET['rid'], $_GET['confirm']])){
			
		
			$recipient->groups = array_keys($this->options['groups']);
			$recipient->active = 1;
			$recipient->confirm_code = 0;
			
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
		
		$sender = $this->config->default_sender;
		$replyto = $this->config->default_replyto;
		$subj = $this->lang['CONFIRM_SUSBSCRIBE'];
		
		
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		if(!$sender)
			die('ERROR NO SENDER CONFIGURED. ERRCODE: 9846a5498g4sf6');
		
		$headers .= "From: $sender\r\n";

		if($replyto)
			$headers .= "Reply-To: $replyto\r\n";

		
		
		if(!mail($subscriber->email, $subj, $body, $headers)) {	
			die('mail send failed');
		}	
		
		return true;
	}
	
	
	function doNewSubscribe()
	{		
		$subscriber = new GW_NL_Subscriber;
		
		$subscriber->setValues(['email'=>$_POST['email'], 'lang'=>$this->app->ln, 'active'=>0, 'unsubscribed'=>0]);
		$subscriber->setConfirmCode();
		
		
		if($subscriber->validate())
		{
			$subscriber->insert();
			
			$this->__sendConfirmMail($subscriber);
			
			
			die('subscribe_ok_confirm');
		}else{
			die(json_encode($subscriber->errors));;
		}
		
		
		
	}
	
	
	
	function viewLink()
	{
		if(!isset($_GET['nlid']) || !isset($_GET['rid']) || !isset($_GET['link']))
			die('Bad link Errcode: 48499444');
		
		$link = base64_decode($_GET['link']);
		
		if($nl = GW::getInstance('GW_NL_Message')->find(['id=?', $_GET['nlid']]))
		{
			$hit=GW::getInstance('GW_NL_Hit')->createNewObject();
			$hit->setValues(
				[
					'message_id'=>$_GET['nlid'],
					'subscriber_id'=>$_GET['rid'],
					'link'=>$link,
					'ip'=>$_SERVER['REMOTE_ADDR'],
					'debug'=>$_SERVER['REQUEST_URI']
				]
			);
			$hit->insert();			
		}
		
		
		Header('Location: '.$link);
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
			$hit=GW::getInstance('GW_NL_Hit')->createNewObject();
			$hit->setValues(
				[
				    'message_id'=>$this->tpl_vars['newsletter_id'],
				    'subscriber_id'=>$item->id,
				    'link'=>$item->unsubscribed ? 'unsubscribe' : 'newsgroup change '.implode(',', $prev_groups).' > '.implode(',', $post_groups),
				    'ip'=>$_SERVER['REMOTE_ADDR'],
				    'debug'=>$_SERVER['REQUEST_URI']
				]
			);
			$hit->insert();
			
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
		
		
		$message = $letter->body_full;
		$message = GW_Link_Helper::trackingLink($message);
		
		$trackinglink="link?nlid={$letter->id}&rid=".$recipient->id.'&link=';
		$message = str_replace('##TRACKINGLINK##', $trackinglink, $message);
		
		
		
		//register hit
		$hit=GW::getInstance('GW_NL_Hit')->createNewObject();
		$hit->setValues(
			[
			    'message_id'=>$letter->id,
			    'subscriber_id'=>$recipient->id,
			    'link'=>'link-newsletter-online',
			    'ip'=>$_SERVER['REMOTE_ADDR'],
			    'debug'=>$_SERVER['REQUEST_URI']
			]
		);
		$hit->insert();		
		
		
		
		echo $this->smarty->fetch('string:'.$message);
		exit;
		
	}	

	

	
}
