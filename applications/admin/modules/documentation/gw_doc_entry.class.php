<?php

define('GW_GALLERY_ITEM_FOLDER', 1);
define('GW_GALLERY_ITEM_IMAGE', 0);

class GW_Doc_Entry extends GW_Composite_Data_Object
{
	var $table = 'gw_documentation';
	
	var $calculate_fields = Array('child_count'=>1, 'path'=>'getPath', 'short_descr'=>1);
	var $default_order = 'type DESC, time DESC';		
	
	function config()
	{
		static $cache;
		
		if($cache)
			return $cache;
			
		$cache = new GW_Config('documentation/');	
		
		return $cache;
	}
	
	function calculateField($key)
	{
		$cache =& $this->cache['calcf'];
		
		if(isset($cache[$key]))
			return $cache[$key];
		
		switch($key)
		{
			case 'child_count':
				$val=(int)$this->count(Array('parent_id=?',$this->get('id')));
			break;
			case 'short_descr':
				if($this->type==1)
					$val=mb_strlen($this->text) < 100 ? $this->text : mb_substr(strip_tags($this->text),100).'...'; 
			break;					
		}
		
		return $cache[$key]=$val;
	}
	
	function getParent()
	{
		return $this->find(Array('id=?', $this->get('parent_id')));
	}
	
	function getParents()
	{
		$arr=Array();
		$item =& $this;
		
		while($item = $item->getParent())
			$arr[]=$item;
			
		return $arr;
	}

	function getChilds($params=Array())
	{
		$id = $this->id ? (int)$this->id : -1;

		$cond = Array('parent_id=?'.($params['type']?' AND type='.(int)$params['type']:'').($params['active']?' AND active':''), $id);

		$p=Array();
		
		if($params['limit'])
			$p['limit']=$params['limit'];
		
		$list = $this->findAll($cond, $p);

		if($params['return_first_only']) 
			return $list[0];
					
		return $list;
	}
	
	function deleteChilds($params=Array())
	{
		$list = $this->getChilds($params);
		
		foreach($list as $item)
			$item->delete();
			
	}

	function getFoldersTree($child_opt=Array())
	{		
		$f  = function($parent, $path) use (&$child_opt, &$f)
		{
			$arr = Array();
			$arr[$parent->get('id')] = $path ? $path : ' / ';
			
			foreach($parent->getChilds(Array('type'=>GW_GALLERY_ITEM_FOLDER)) as $item)
				$arr+=$f($item, $path.' / '.strip_tags($item->get('text')));

			return $arr;
		};
			
		return $f($this->createNewObject(-1), '');
	}
	
	function &getParentOpt()
	{
		$list = $this->getFoldersTree();
		
		unset($list[$this->get('id')]);
		
		return $list;
	}
	
	function getPath()
	{
		$list = $this->getParents();
		$path='';
		for($i=count($list)-1;$i<=0;$i--)
			$path.= '/'. $list[$i];
		
		return $path?'/':$path;
	}
	
	
	function eventHandler($event, &$context_data=[])
	{
		switch($event)
		{
			case 'BEFORE_DELETE':
				$this->deleteChilds();
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}	
}