<?php

class Module_NewsLetter extends GW_Public_Module
{

	function init()
	{
		$this->subscriber = new GW_NL_Subscriber;
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
	}


	function viewDefault($params)
	{
		//dump($this->lang);
		//exit;		
		
	}
	
	function viewSubscribe()
	{		
		
		//d::Dumpas($_GET['rid']);
		//d::Dumpas($_GET['rid']);
		
		$nlid=$_GET['nlid'];
		$rid=$_GET['rid'];
		$rid=  base64_decode($rid);
		
		
		$subscriber = $this->subscriber->find(['email=?', $rid]);
		
		
		
		if(!$subscriber)
		{
			$this->app->setMessage("nerasta");
			
			return false;
		}
		
		
		//d::dumpas([$subscriber, $subscriber->groups, $subscriber->groups]);
		//$this->smarty->assign();
		
		//d::dumpas([$nlid, $rid]);
		return ['subscriber'=>$subscriber];
		
		
		
	}
	
	function doTest()
	{		
		d::ldump('doTest');
		
	}

	

	
}
