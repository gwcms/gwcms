<?php


class Module_CartItems  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		$this->model = GW_Cart_Item::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=false;	
		

		$this->cartgroup_id = $this->app->path_arr['1']['data_object_id'] ?? false;
		$this->cartgroup = GW_Cart_Group::singleton()->find(['id=?', $this->cartgroup_id]);
		
		if($this->cartgroup)
		{
			$this->filters['group_id']=$this->cartgroup_id;
		}

	}
	

	
	
	
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'obj_type'=> 'Lof',
			'obj_id'=> 'Lof',
			'modpath' => 'L',
			'unit_price' => 'Lof',
			'qty' => 'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',		    
			]
		);
		
		//$cfg['filters']['project_id'] = ['type'=>'select','options'=>$this->options['project_id']];
					
		return $cfg;
	}
	
	function __eventAfterList(&$list)
	{
		///$this->attachFieldOptions($list, 'composer_id', 'IPMC_Composer');
		
		
		//$pieces0 = IPMC_Competition_Pieces::singleton();
	
		foreach($list as $item)
			$this->initType($item);		
	}

	
	function __eventBeforeSave($item)
	{
		
		//d::dumpas($item);
		//$item->admin_id = $this->app->user->id;
	}
		
	
	function initType($item)
	{
		$class = $item->obj_type;
		
		static $cache;
		
		if(!isset($cache[$class])){
			$pages = GW_ADM_Page::singleton()->findALL(['info LIKE "%'.GW_DB::escape($class).'%"']);

			if(count($pages)==1){
				$cache[$class] = $pages[0]->path;
			}
		}
	
		
		$item->modpath = $cache[$class] ?? false;
				
	}
	
	function viewForm()
	{
		$vars = parent::viewForm();
		
		$this->initType($vars['item']);
		
		return $vars;
	}


	
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
}
