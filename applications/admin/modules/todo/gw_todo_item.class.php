<?php



class GW_Todo_Item extends GW_Composite_Data_Object
{
	var $table = 'gw_todo';
	var $composite_map = Array
	(
		//'image' => Array('gw_image', Array('dimensions_resize'=>'6000x4000', 'dimensions_min'=> '10x10')),
		'file1'=>['gw_file']
	);
	
	var $calculate_fields = Array('child_count'=>1, 'comments_count'=>1, 'path'=>'getPath');
	var $default_order = 'state ASC, priority DESC';	

	public $attachments_owner = 'todo/items';
	public $extensions = ['attachments'=>1];
	

	
	function calculateField($key)
	{
		switch($key)
		{
			case 'child_count':
				$val=(int)$this->count(Array('parent_id=?',$this->get('id')));
			break;
			case 'comments_count':
				$val=(int)$this->count(Array('parent_id=? AND type=2',$this->get('id')));
			break;		
		}
		
		return $val;
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
		$type = $params['type']?? 0;
		$actv = $params['active'] ?? false;
		$cond = Array('parent_id=?'.($type?' AND type='.(int)$type:'').($actv ?' AND active':''), $id);

		$p=Array();
		
		if($params['limit'] ?? false)
			$p['limit']=$params['limit'];
		
		$list = $this->findAll($cond, $p);

		if($params['return_first_only'] ?? false) 
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
	

	
	function eventHandler($event, &$context_data=[])
	{
		switch($event)
		{
			case 'BEFORE_DELETE':
				$this->deleteChilds();
			break;
		
			case 'BEFORE_SAVE':
				if(!is_numeric(!$this->time_have))
					$this->time_have=  GW_Math_Helper::uptimeReverse($this->time_have);
				
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}	
	
	function hasAttachments()
	{
		if($this->file1)
			return true;
	}	
}