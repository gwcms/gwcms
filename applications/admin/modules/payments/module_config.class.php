<?php


class Module_Config extends GW_Module_Config_Common
{	
	public $options=[];
	
	function init()
	{
		$this->model = $this->config = new GW_Config($this->module_path[0].'/');
		$this->initLogger();
		
		
		$this->features = array_fill_keys((array)json_decode($this->config->features), 1);
		

		
		parent::init();
		
				
		if($this->feat('itax')){
			$this->addRedirRule('/^doItax|^viewItax/i','itax');
			$this->addRedirRule('events','itax');
		}		
	}
	
	function doCronRun()
	{
		$this->config = $this->model;
		
		$mins = $_GET['every'];
		$tasks = $this->config->get("tasks_{$mins}min");
		$tasks = explode(';', $tasks);
		$t = new GW_Timer;
		
		foreach($tasks as $task)
			if($task){
				if(substr($task, 0,1)=='#'){
					$task = substr($task, 1);
					$url=Navigator::backgroundRequest('admin/lt/shop/'.$task, ["cron"=>1]);
				}else{
					$req = Navigator::buildURI("admin/lt/shop/".$task, ["cron"=>1]);

					//async
					//$url=Navigator::backgroundRequest($req);
					//synchronous
					$resp = Navigator::sysRequest($req);

					$this->setMessage($req.': '.json_encode($resp));						
				}
					

			}
		
		$this->setMessage("Took ".$t->stop().' secs');	
			
		//$this->msg('translate 300');
		
	}
}
