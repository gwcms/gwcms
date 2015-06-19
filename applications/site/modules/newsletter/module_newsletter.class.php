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
		$rid=$_GET['rid'];
		$rid=  base64_decode($rid);
		
		
		$subscriber = $this->subscriber->find(['email=?', $rid]);
		
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
			if($_POST['email']!=$item->email)
			{
				$this->app->setErrors("Neteisingai nurodytas el. pašto adresas");
				$success = false;
			}
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

	

	
}
