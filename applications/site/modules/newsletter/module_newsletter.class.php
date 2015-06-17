<?php

class Module_NewsLetter extends GW_Public_Module
{

	function init()
	{
		$this->model = new GW_NL_Subscriber;
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
		
		d::dumpas([$nlid, $rid]);
		
		
		
	}
	
	function doTest()
	{		
		d::ldump('doTest');
		
	}

	

	
}
