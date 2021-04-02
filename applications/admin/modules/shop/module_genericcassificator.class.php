<?php

class Module_GenericClassificator extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		//$i = Shop_Products::singleton()->getFieldInfoBy('obj', get_class($this->model));
		//$this->tpl_vars['prod_field'] = $i["field"];
		
		$this->initLogger(); // jei per cron leisim kad uzlogintu zinutes
		
	}
	function doCounts()
	{		
		$i = Shop_Products::singleton()->doCounts(get_class($this->model));
		$this->setMessage("Done counting. Affected: {$i['affected']}. Speed: {$i['speed']} s");
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

