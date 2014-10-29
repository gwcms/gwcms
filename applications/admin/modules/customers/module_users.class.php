<?php

class Module_Users extends GW_Common_Module 
{

	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function eventHandler($event, &$context) 
	{
		switch($event)
		{
			case "BEFORE_SAVE_0":
				$context->setValidators('update');
			break;
		}
		
		parent::eventHandler($event, $context);
	}


}

?>
