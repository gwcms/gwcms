<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw_adm_page
 *
 * @author wdm
 */
class GW_Adm_Page_View extends GW_Data_Object
{
	public $table = 'gw_adm_page_views';
	public $default_order = 'type ASC, priority DESC, title ASC';
	
	public $current = false; //used in controller
	public $count_result = false; //used in controller
	
	public $validators = [
	    'title'=>['gw_string', ['required'=>1]],
	];	
	
	/**
	 * 
	 * @param array $paths
	 */
	function getByPath($paths)
	{
		//d::dumpas("active=1 AND ".GW_DB::inConditionStr("path", $paths));
		
		return $this->findAll("active=1 AND ".GW_DB::inConditionStr("path", $paths), ['order'=>'priority DESC, title ASC']);
	}
	
	
	static function selectDefault($piewsArray)
	{
		foreach($piewsArray as $pview)
		{
			if($pview->default)
				return $pview;
		}
		
		//get first array elm
		reset($piewsArray);
		return current($piewsArray);
	}
	
	static function selectById($piewsArray, $id)
	{
		foreach($piewsArray as $pview)
		{
			if($pview->id==$id)
				return $pview;
		}		
	}
	
	//surinkti pasleptus ir matomus viewsus
	static function select2Display($piewsArray, $dropdown = false, $type="")
	{
		$arr = [];
		
		foreach($piewsArray as $pview)
		{
			if($pview->type!=$type)
				continue;
			
			if($dropdown){
				if($pview->dropdown && !$pview->current)
					$arr[] = $pview;
			}else{
				if(!$pview->dropdown || $pview->current)
					$arr[] = $pview;
			}
		}

		return $arr;
	}	
}