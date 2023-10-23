<?php



class Module_Executors extends GW_Common_Module
{		
	public $default_view = 'list';
	
	function init()
	{
	
		parent::init();	
	}	
	
	
	function viewDefault()
	{
		
	}
	
	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->title;  },
		    'search_fields'=>['title','email']
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
		$ctx['dst']->title = $ctx['dst']->title.' (copy of #'.$ctx['src']->id.')';
	}
		
	function __eventAfterClone($ctx)
	{
		$source = $ctx['src'];
		$dest = $ctx['dst'];
		
		$cnt = 0;
		foreach($source->execprice as $subitem){
			$subvals = $subitem->toArray();
			unset($subvals['id']);
			
			$subitemnew = $subitem->createNewObject($subvals);
			$subitemnew->owner_id = $dest->id;
			$subitemnew->insert();
			$cnt ++;
		}
		
		$this->setMessage("Subelements copy count: $cnt");
	}
	
	function __eventBeforeDelete($item)
	{
		$this->recoveryEmail($item);
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();	
		$cfg['fields']['execprices'] = "l";
		$cfg['fields']['shipprices'] = "l";	
		
		return $cfg;
	}
}
