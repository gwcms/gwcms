<?php


class Module_CartGroups extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		$this->model = GW_Cart_Group::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		$this->app->carry_params['search']=1;
		$this->app->carry_params['composer_id']=1;
		$this->app->carry_params['clean']=1;
		
		if(isset($_GET['composer_id'])){
			$this->filters['composer_id'] = (int)$_GET['composer_id'];
			$this->list_params['paging_enabled']=0;	
		}
		
		
	}
	

	
	
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';		
		//$cfg['filters']['catalog_type'] = ['type'=>'select','options'=>$this->options['catalog_type']];
		//$cfg['filters']['tonality'] = ['type'=>'select','options'=>$this->options['tonality']];
		//$cfg['filters']['instruments'] = ['type'=>'select','options'=>$this->options['instrument_id'], 'ct'=>['LIKE%,,%'=>'==']];
		
		//d::dumpas($cfg);
					
		return $cfg;
	}
	
	function __eventAfterList(&$list)
	{		
		$this->attachFieldOptions($list, 'user_id', 'GW_User');
		

		foreach($list as $item)
		{
			$item->items_count = GW_Cart_Item::singleton()->count(['group_id=?', $item->id]);
		}	
	}
	
	function __eventAfterForm($item)
	{
		$list=[$item];
		
		//if(isset($_GET['composer_id']))
		//	$item->composer_id = $_GET['composer_id'];
		
		//if($item->composer_id)
		//	$this->attachFieldOptions($list, 'composer_id', 'IPMC_Composer', ['simple_options'=>'title_'.$this->app->ln]);
	}
	
	function overrideFilterUser_title($value, $compare_type)
	{	
		$x=$this->__overrideFilterExObject("GW_User", "user_id", ["name","surname",'email'], $value, $compare_type);
		
		
		return $x;
	}
	
	/*
	function overrideFilterInstruments($value, $compare_type)
	{	
		d::Dumpas([$compare_type, $value]);
		
		$compare_type = "LIKE%,,%";
		
		return $x;
	}	*/
	
	
	
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
