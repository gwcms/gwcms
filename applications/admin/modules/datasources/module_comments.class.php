<?php



class Module_Comments extends GW_Common_Module
{	
  	function init()
  	{
		$this->model = GW_Comments::singleton();
		
  		parent::init();
  		
		
 		$this->filters['obj_id']= $_GET['obj_id'];
 		$this->filters['obj_type']= $_GET['obj_type'];
		

		
		//d::dumpas($this->filters);
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['obj_id']=1;
		$this->app->carry_params['obj_type']=1;
		
		$this->model->default_order = 'id ASC';
		
		
  	}
  	
  	
  	function __eventAfterList($list)
  	{
		if(isset($_REQUEST['id']))
  		{
  			$comment = $this->model->createNewObject($_REQUEST['id']);
  			$comment->load();
  			$this->tpl_vars['comment'] =& $comment; 
  		}	
  	}

	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case "BEFORE_SAVE":
				$item = $context;
				
				$item->user_id = $this->app->user->id;
				
				$item->comment = str_replace("\n", "<br />", $item->comment);
				
			break;
			case 'AFTER_FORM':
				$item = $context;
				$item->comment = str_replace('<br />',"\n", $item->comment);
			break;
		}
		
		parent::eventHandler($event, $context);
	}
	


	
	function getListConfig()
	{
//		/,,
		return ['fields'=>[
		    'comment'=>'L',
		    'user_id'=>'L',
		    'insert_time'=>'L',
		]];
		
	}
	
	
	
	function canBeAccessed($item, $opts = array()) {
		
		if(!$item->id)
			return true;
		
		$requestAccess = $opts['access'] ?? GW_PERM_WRITE;
		
		$availAccess = GW_PERM_READ;
		
		if($item->user_id == $this->app->user->id){
			$availAccess =  GW_PERM_READ | GW_PERM_WRITE;
		}
		
		$result = $availAccess & $requestAccess;
		
		//d::dumpas([$item->user_id, $this->app->user->id, $requestAccess, $availAccess, $result]);
		
		if (isset($opts['nodie']) || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		$this->jump();		
	}
}