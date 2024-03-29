<?php

define('GW_GALLERY_ITEM_FOLDER', 1);
define('GW_GALLERY_ITEM_IMAGE', 0);

class GW_Gallery_Item extends GW_i18n_Data_Object
{
	public $table = 'gw_gallery_items';
	public $composite_map = Array
	(
		'image' => Array('gw_image', Array('dimensions_resize'=>'6000x4000', 'dimensions_min'=> '10x10')),
	);
	
	public $calculate_fields = Array('child_count'=>1, 'path'=>'getPath');
	public $default_order = 'type DESC, priority ASC';		
	public $order_limit_fields=['parent_id'];
	
	public $i18n_fields = Array(
		'title' => 1,
		'description' => 1,
	);	
	
	
	function config()
	{
		static $cache;
		
		if($cache)
			return $cache;
			
		$cache = new GW_Config('gallery/');	
		
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

	function getChilds($params=[])
	{
		if(isset($params['ids'])){
			$cond = GW_DB::inCondition("id", $params['ids']);
		}else{
			$id = $this->id ? (int)$this->id : -1;
			$cond = "parent_id = ".(int)$id;
		}
		
		
		
		$cond = $cond.
			(isset($params['type'])?' AND type='.(int)$params['type']:'').
			(isset($params['site_id'])?' AND site_id='.(int)$params['site_id']:'').
			(isset($params['active'])?' AND active':'');

		$p=[];
		
		if(isset($params['limit']))
			$p['limit']=$params['limit'];
		
		$list = $this->findAll($cond, $p);

		if(isset($params['return_first_only'])) 
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
				$arr+=$f($item, $path.' / '.$item->get('title'));

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
	
	function getStoreSize()
	{
		static $cache;
		if($cache)
			return $cache;
			
		return $cache = $this->config()->store_size;
	}
	
	function eventHandler($event, &$context_data=[])
	{
		switch($event)
		{
			case 'AFTER_CONSTRUCT':
				$this->composite_map['image'][1]['dimensions_resize']=$this->getStoreSize();
			break;
			
			case 'BEFORE_DELETE':
				$this->deleteChilds();
			break;
		}
		
		parent::EventHandler($event, $context_data);
	}	
}