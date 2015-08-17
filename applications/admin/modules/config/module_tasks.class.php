<?php


class Module_Tasks extends GW_Common_Module
{	

	public $default_view = 'list';	
	
	function init()
	{
		parent::init();
		$this->list_params['paging_enabled']=1;
	}

	
	
	function __eventBeforeList()
	{	

		
		
		$this->loadTasksList();		
		
		if(!GW_App_System::getRunningPid())
			$this->setErrors("admin/cli/system.php is not running", 2);
	}

	
	function loadTasksList()
	{
		$tasks = glob(GW::s('DIR/ADMIN').'cli/tasks/*'); 
		
		foreach($tasks as $i => $task)
			$tasks[$i] = str_replace('.task.class','' ,pathinfo($task, PATHINFO_FILENAME));
			
		$this->tpl_vars['tasks']= $tasks;	
	}	

	/*Nusiusti signala system procesui vykdyti uzduoti*/
	
	function doRunTask()
	{
		GW_Task::addSingleStatic($_REQUEST['task'], Array('debug'=>1));

		$this->jump();
	}
	
	/* Paleisti uzduoti ir gauti atsakyma narsykleje*/
	
	function doRunTaskDirect()
	{
		GW_Tasks_App::runDirect($_REQUEST['task'], Array('debug'=>1));
	}	

	function doRemoveAll()
	{
		while($list = $this->model->findAll('running=0',Array('limit'=>1000)))
		{
			foreach($list as $item)
				$item->delete();
				
			$count +=count($list);
		}
			
		$this->app->setMessage("Removed items: ".$count);
		$this->jump();
	}
	
	function doHaltTask()
	{
		$item = $this->getDataObjectById();
		
		$_REQUEST['id']=$item->running;
		
		$this->dohaltProc();
	}	
	
	function doRestartSystem()
	{
		GW_App_System::runSelf();
		$this->jump();
	}
	
	function viewProcesses()
	{
		$_GET['clean']=1;
		$this->list_params['paging_enabled']=0;
		
		$list = GW_Dummy_Data_Object::buildListStatic(GW_Proc_Ctrl::getRunningProcesses());
		
		return Array('list'=>$list);
	}
	
	function doHaltProc()
	{
		$pid=$_REQUEST['id'];
		
		$all = GW_Proc_Ctrl::getRunningProcesses();
		
		if(!isset($all[$pid]))
		{
			$this->setErrors("Fail to terminate. Process ($pid) not found");
			$this->jump();
		}
		
		if($_REQUEST['sigkill'])
			$return=posix_kill($pid, 9); //hard
		else
			$return=posix_kill($pid, 15); //soft
			
			
		$this->app->setMessage("Task done: ".$return);
		
		$this->jump();
	}
	
	
	function doShowLogs()
	{
		$taskname=$_REQUEST['task'];
		
		$this->list_params['filters']['name']=Array('=',$taskname);
		
		$this->jump();
	}
}

