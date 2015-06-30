<?php

class Module_NewsLetter extends GW_Public_Module
{

	function init()
	{
		parent::init();
		
		$this->subscriber = new GW_NL_Subscriber;
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
		
	}


	function viewDefault($params)
	{
		//dump($this->lang);
		//exit;		
	}
	
	function __initSubscriber()
	{
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
				    'ip'=>$_SERVER['REMOTE_ADDR']
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
				    'ip'=>$_SERVER['REMOTE_ADDR']				    
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
			    'ip'=>$_SERVER['REMOTE_ADDR']				    
			]
		);
		$hit->insert();		
		
		
		
		echo '<html><meta charset="UTF-8">';
		echo $this->smarty->fetch('string:'.$message);
		exit;
		
	}	

	

	
}
