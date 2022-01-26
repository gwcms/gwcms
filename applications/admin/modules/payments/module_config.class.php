<?php


class Module_Config extends GW_Common_Module
{	
	public $default_view = 'default';
	
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
	
	function viewDefault()
	{
				
		return ['item'=>$this->model];
		
	}	
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		foreach($vals as $key => $val)
			if(is_array($val))
				$vals[$key] = json_encode($val);
			
			
		$this->fireEvent("BEFORE_SAVE", $vals);
		
		$this->model->setValues($vals);
		
		$this->fireEvent("AFTER_SAVE", $this->model);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		//$this->__afterSave($vals);
		$this->jump();
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