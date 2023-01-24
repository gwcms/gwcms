<?php



class Module_VATgroups extends GW_Common_Module
{		
	
	function init()
	{
	
		parent::init();	
	}	
	
	
		
	
	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->title;  },
		    'search_fields'=>['title']
		];	
		    
		//if(isset($_GET['byCode'])){
		
		   //neteisingas sprendimas, yra 0% grupe
		//$opts['idx_field'] = 'key';
				    
		
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
		//$ctx['dst']->code = Shop_DiscountCode::singleton()->getUniqueCode();
		//$ctx['dst']->used = 0;
		//$ctx['dst']->user_id = 0;
	}
		


	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		
		
		$cfg['inputs']['title']=['type'=>'text'];	
		$cfg['inputs']['key']=['type'=>'text'];	

		$cfg['inputs']['active']=['type'=>'bool'];	
		
		$cfg['inputs']['percent']=['type'=>'number'];	
		
		$cfg['inputs']['note']=['type'=>'text'];

		
		

		
		
		///		modpath="datasources/countries"

		//$cfg['inputs']['country']=['type'=>'select_ajax', 'modpath'=>"datasources/countries", 'empty_option'=>1, "source_args"=>["byCode"=>1]];	

		
		
		return $cfg;
		
		//return ;
	}
	
}
