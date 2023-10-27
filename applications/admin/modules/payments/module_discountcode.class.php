<?php



class Module_DiscountCode extends GW_Common_Module
{		
	
	function init()
	{
	
		parent::init();	
	}	
	
	
		
	
	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->title;  },
		    'search_fields'=>['code']
		];	
		
		return $opts;	
	}
	
	
	
	function __eventAfterList(&$list)
	{
		/*
		foreach($list as $item){
			$item->element_count = GW_Form_Elements::singleton()->count('owner_id='.(int)$item->id);
			$item->answer_count = GW_Form_Answers::singleton()->count('owner_id='.(int)$item->id);
		}
		*/
	}
	
	

	function __eventBeforeClone($ctx)
	{		
		$ctx['dst']->code = Shop_DiscountCode::singleton()->getUniqueCode();
		$ctx['dst']->used = 0;
		$ctx['dst']->user_id = 0;
	}
		

	function __eventBeforeDelete($item)
	{
		$this->recoveryEmail($item);
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['changetrack'] = 'L';
	
		$cfg['filters']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
		$cfg['inputs']['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users'];
	
		return $cfg;
	}	
}
