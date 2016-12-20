<?php

class Module_Users extends GW_Common_Module 
{

	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
		$this->cfg = new GW_Config($this->module_path[0].'/');
		
		
		$this->rootadmin = $this->app->user->isRoot();
		

		
		
		//d::dumpas($this->options['languages']);
		
		$this->list_params['paging_enabled']=1;
		

	}
	
	function __eventAfterForm($item)
	{
		
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function doLoginAs()
	{
		$_SESSION[PUBLIC_AUTH_SESSION_KEY] = ['user_id'=>$_GET['user_id'], 'ip_address'=>$_SERVER['REMOTE_ADDR']];
		
		Header('Location: '.Navigator::getBase().$_GET['redirect_url']);
	}
	
	function eventHandler($event, &$context) 
	{
		switch($event)
		{
			case "BEFORE_SAVE_0":
				
				$item = $context;
				
				if($item->id){
					$item->setValidators('update');
				}else{
					$item->setValidators('insert');
					$item->group_ids = [$this->cfg->customer_group];
					
					$item->parent_user_id = $this->app->user->id;
				}
				
				
				
			break;
		}
		
		parent::eventHandler($event, $context);
	}

}

?>
