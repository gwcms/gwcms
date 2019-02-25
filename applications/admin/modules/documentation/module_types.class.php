<?php


class Module_Types extends GW_Common_Module
{	
	
	function init(){
		parent::init();
		
		
		$this->app->carry_params=['clean'=>1];
	}
	
	
	function viewOptions()
	{
		
		$cond = isset($_GET['q']) ? GW_DB::prepare_query(['title LIKE ?', '%'.$_GET['q'].'%']) : false;
		
		$opts = $this->model->getOptions($cond);
		
		$list = [];
		
		foreach($opts as $id => $text)
			$list['items'][]=["id"=>$id, "title"=>$text];
		
		echo json_encode($list);
		exit;
	}
	
	
	//disable filtering feature
	function prepareListConfig()
	{
		parent::prepareListConfig();
		
		$this->list_config['dl_filters'] = [];
	}


	//dont show some fields if it isnt asked
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		//dont show at first time
		foreach(['id','insert_time','update_time'] as $field)
			$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		return $cfg;
	}
	
	
	
}