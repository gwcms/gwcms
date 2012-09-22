<?


class Module_Tasks extends GW_Common_Module
{	

	function init()
	{
		parent::init();
		$this->list_params['paging_enabled']=1;
	}

	
	
	function viewDefault()
	{	
		$this->viewList();

		$this->loadTasksList();		
		
		if(!GW_App_System::getRunningPid())
			$this->setErrors("admin/cli/system.php is not running", 2);
	}

	
	function loadTasksList()
	{
		$tasks = glob(GW::$dir['ADMIN'].'cli/tasks/*'); 
		
		foreach($tasks as $i => $task)
			$tasks[$i] = str_replace('.task.class','' ,pathinfo($task, PATHINFO_FILENAME));
			
		$this->smarty->assign('tasks', $tasks);	
	}	

	function doRunTask()
	{
		GW_Task::addSingleStatic($_REQUEST['task'], Array('debug'=>1));

		$this->jump();
	}
	
	function doRunTaskDirect()
	{
		GW_Tasks_App::runDirect($_REQUEST['task'], Array('debug'=>1));
	}	

	function doRemoveAll()
	{
		$list = $this->model->findAll('running=0');
		
		foreach($list as $item)
			$item->delete();
			
		GW::$request->setMessage("Removed items: ".count($list));
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
	
	function dohaltProc()
	{
		$pid=$_REQUEST['id'];
		
		$all = GW_Proc_Ctrl::getRunningProcesses();
		
		if(!isset($all[$pid]))
		{
			$this->setErrors("Fail to terminate. Process ($pid) not found");
			$this->jump();
		}
		
		if($_REQUEST['sigkill'])
			$return=posix_kill($pid, 9);
		else
			$return=posix_kill($pid, 15);
			
			
		GW::$request->setMessage("Task done: ".$return);
		
		$this->jump();
	}
}

