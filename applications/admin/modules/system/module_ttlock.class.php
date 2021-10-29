<?php


class Module_TTlock extends GW_Common_Module
{	
	public $default_view = 'default';
		
	function init()
	{
		$this->model = new stdClass();
		
		
		parent::init();
	}

	
	function viewDefault()
	{
	
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
		
		

	}
	
	
	
	public $doTestList = ["info"=>"Get list of devices"];	
	
	function doTestList()
	{
		
		$r = ttlock_api::singleton()->init()->getLockList();
		d::ldump($r);
		
		//nera tokio
		//$r = ttlock_api::singleton()->init()->listPasscode();
		//d::ldump($r);
	}
	
	public $doTestAddPassCode = ["info"=>"Set time limited passcode (123456) from now+1minute - 20min long"];	
	
	function doTestAddPassCode()
	{
		$passcode="123456";
		$r = ttlock_api::singleton()->init()->addPasscodeRandom(false,$passcode,6,strtotime('+1 MINUTE'),strtotime("+20 MINUTE"));
		
		d::ldump([$r,['code'=>$passcode]]);
	}
	
	public $doTestDeletePasscode = ["info"=>"Delete passcode"];	
	
	function doTestDeletePasscode()
	{
		$form = ['fields'=>['field'=>[
			'passid'=>['type'=>'text']
		    ]],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/ACTION_REQUESTS_ADDITIONAL_INPUT'))))
			return false;		
		
		
		
		
		ttlock_api::singleton()->init()->deletePasscode($answers['passid']);
		//api.ttlock.com/v3/keyboardPwd/delete
		
		
		
	}
	
	
}
