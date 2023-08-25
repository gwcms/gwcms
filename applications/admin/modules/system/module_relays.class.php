<?php


class Module_Relays extends GW_Common_Module
{	
	public $default_view = 'default';
		
	function init()
	{
		$this->model = new stdClass();
		$this->cfg = $cfg = new GW_Config("system__relays/");	
		$cfg->preload('');
		
		
		
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
		
		
		$this->tpl_vars['states'] = $this->getStates();

	}
	
	function doSet()
	{
		file_get_contents($url=$this->cfg->endpoint.'set.php?'. http_build_query(['id'=> $_GET['id'], 'state'=> $_GET['state']]));
		$this->jump();
	}
	
	
	
	public $doTestList = ["info"=>"Get list of devices"];	
	
	
	function getStates()
	{
		
		return json_decode(file_get_contents($this->cfg->endpoint.'states.php'), TRUE);
	}
	
	public $doTestStates = ['info'=>'Test retrieve states'];
	
	function doTestStates()
	{
		$statuses = file_get_contents($this->cfg->endpoint.'states.php');
		d::ldump($statuses);
	}
	
	public $doTestRealStates = ['info'=>'Test retrieve real states'];
	
	function doTestRealStates()
	{
		$statuses = file_get_contents($this->cfg->endpoint.'realstates.php');
		d::ldump($statuses);
	}	
	
	public $doTestSet = ['info'=>'Test state change'];
	
	function doTestSet()
	{	
		
		$opts = [];
		
		for($i=(int)$this->cfg->relays_toogle_from; $i<=(int)$this->cfg->relays_toogle_to;$i++)
			$opts[$i] = $i;
	

		$form = ['fields'=>[
		    'relay_id'=>['type'=>'select','options'=>$opts, 'options_fix'=>1, 'empty_option'=>1, 'required'=>1],
		    'state'=>['type'=>'bool']
		],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Select relay and state")))
			return false;	
		
		
		$statuses = file_get_contents($url=$this->cfg->endpoint.'set.php?'. http_build_query(['id'=> $answers['relay_id'], 'state'=> $answers['state']]));
		d::ldump([$url,$statuses]);
	}
	
	
	public $doTestUpdateSchedule = ['info'=>'Test upload schedule'];
	
	function doTestUpdateSchedule()
	{
		$schedule = file_get_contents($this->cfg->schedule_source);
		Navigator::getUri();
		
		//radau kad is svetaines kas penkias minutes siuncia atnaujinima ir paciame kompiuteryje vyksta kas minute atnaujinimas tai pasalinau ta kur is svetaines siuncia
		//pagalvojau kad galimai toks galejo but sutapimas kad vienu metu tapati daro ir susipjauna			
		//$resp = GW_Http_Agent::singleton()->postRequest($this->cfg->endpoint.'set.php?run=1', ['schedule'=>$schedule]);
		
		$resp = GW_Http_Agent::singleton()->postRequest($this->cfg->endpoint.'set.php', ['schedule'=>$schedule]);
		
		
		d::ldump($resp);
	}
	
	
	public $doTestplay = ['info'=>'Play with lights for fun'];
	
	function doTestplay()
	{
		$t = new GW_Timer;
		$statuses = file_get_contents($url=$this->cfg->endpoint.'play.php');
		$this->setMessage("Time took {$t->stop(3)}");
		$this->jump();
	}
	
	
}
