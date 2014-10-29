<?php



class ToDo_Item extends GW_Composite_Data_Object
{
	var $table = 'gw_todo';
//	var $composite_map = Array
//	(
//		'image' => Array('gw_image', Array('dimensions_resize'=>'6000x4000', 'dimensions_min'=> '10x10')),
//	);
	
	var $calculate_fields = Array('child_count'=>1, 'path'=>'getPath');
	var $default_order = 'priority DESC, state ASC';		
	

	
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
			
			foreach($parent->getChilds(Array('type'=>1)) as $item)
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
	

	
	function eventHandler($event)
	{
		switch($event)
		{
			case 'BEFORE_DELETE':
				$this->deleteChilds();
			break;
		}
		
		parent::EventHandler($event);
	}	
}