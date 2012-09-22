<?
/**
 * 
 * @author vidmantas
 *	
 */


class GW_ADM_Page extends GW_i18n_Data_Object
{
	var $table = 'gw_adm_sitemap';
	var $i18n_fields = Array('title'=>1);
	var $validators = Array('views'=>'gw_json');

	var $default_order = 'priority ASC';
	var $level=0;
	var $data_object;
	
	var $encode_fields=Array('info'=>'serialize', 'fields'=>'serialize');
	var $ignore_fields = Array('data_object_id'=>1);
	var $calculate_fields = Array('VIEWS'=>'getViews');

	function getViews()
	{
		return json_decode($this->get('views'), true);
	}
	
	function getChilds($params=Array())
	{
		$menu = isset($params['menu']) ? $params['menu'] : true;
		$check_perm	= isset($params['check_permissions']) ? $params['check_permissions'] : true;			
		
		$cond = Array('parent_id=?'.($menu?' AND active AND in_menu':''), (int)$this->id);

		$list_0 = $this->findAll($cond);
		$list = Array();
		
		foreach($list_0 as $i => $item)
			if(!$check_perm || $item->canAccess() || $item->getChilds($params))
			{
				if($params['return_first_only']) 
					return $item;
						
				$list[] = $item; 
			}
			
		return $list;
	}

	function getByPath($path)
	{
		if($data_object_id=(int)pathinfo($path,PATHINFO_FILENAME))
			$path=dirname($path);
		
		$path = preg_replace('/\/\d+\//','/',$path);
		
		if(($tmp = $this->find(Array('path=?',$path))) && $data_object_id)
			$tmp->set('data_object_id', $data_object_id);
			
		return $tmp;
	}
	
	function canAccess()
	{
		if((bool)(int)$this->get('public'))
			return true;
		
		if(!GW::$user)
			return false;
			
		if(GW::$user->isRoot())
			return true;
		
		return $this->get('active') && GW_ADM_Permissions::canAccess($this->get('path'), GW::$user->group_ids);
	}
	
	function getFirstChild()
	{
		if($item = $this->getChilds(Array('return_first_only'=>1)))
			return $item;
			
		return false;
	}
	
	function deleteChilds()
	{
		$list = $this->findAll("path LIKE '".GW_DB::escape($this->path)."/%'");
		
		foreach($list as $item)
			$item->delete();
	}

	function eventHandler($event)
	{
		switch($event)
		{
			case 'AFTER_LOAD':
				$this->level = substr_count($this->get('path'), '/');
			break;

			case 'BEFORE_SAVE':
				//is formos jei ateina json tekstas

			break;			
			
			case 'BEFORE_DELETE':
				$this->deleteChilds();
			break;
			
			case 'AFTER_DELETE':
				GW_ADM_Permissions::deleteByPath($this->get('path'));
			break;
		}
		
		parent::eventHandler($event);
	}
	
	function getDataObject()
	{
		if($cache =& $this->cache['data_object'])
			return $cache;
		
		$info=$this->get('info');
		
		if(!$Class=$info['model'])
			return false;
			
			
		$class=strtolower($Class);
		$class=str_replace(Array('/','\\'),'',$class);
		
		list($dir) = explode('/', $this->get('path'));
		$dir=GW::$dir['MODULES'].$dir.'/';
		
		//if file will be stored in lib dir, it will be included automaticly
		//check is file stored in module dir 
		if(file_exists($file="{$dir}{$class}.class.php"))
			include_once $file;
			
		$cache=new $Class();
		
		if($this->get('data_object_id')){
			$cache->set('id', $this->get('data_object_id'));
			$cache->load();
		}

		return $cache;
	}
	
	
	
	
}
