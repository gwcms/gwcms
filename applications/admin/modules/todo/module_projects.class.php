<?php


class Module_Projects extends GW_Common_Module
{	
	
	
	function getListConfig()
	{
		//{$dl_fields=[title,insert_time,update_time]}
		
		$cfg = array('fields' => [
			'title' => 'Lof', 
			'insert_time' => 'Lof', 
			'update_time' => 'Lof', 
			'description' => 'lof'
			]
		);
		
		$cfg['filters']['project_id'] = ['type'=>'select','options'=>$this->options['project_id']];
			
			
		return $cfg;
	}	
	
	
}