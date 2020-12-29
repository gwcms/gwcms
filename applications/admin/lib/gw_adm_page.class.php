<?php
/**
 * 
 * @author vidmantas
 *	
 */


class GW_ADM_Page extends GW_i18n_Data_Object
{
	public $table = 'gw_adm_sitemap';
	public $i18n_fields = ['title'=>1];
	public $validators = ['views'=>'gw_json','orders'=>'gw_json'];

	public $default_order = 'priority ASC';
	public $level=0;
	public $data_object;
	
	public $encode_fields=['info'=>'jsono'];
	public $ignore_fields = ['data_object_id'=>1];
	public $calculate_fields = ['VIEWS'=>'getViews', 'ORDERS'=>'getOrders'];
	public $skip_i18next = true;

	function getViews()
	{
		
		if(!$this->get('views')){
			$info=$this->get('info');
			if(isset($info['default_views']))
				return json_decode($info['default_views'], true);
		}
		
		return json_decode($this->get('views'), true);
	}
	
	function getOrders()
	{
		
		if(!$this->get('orders')){
			$info=$this->get('orders');
			if(isset($info['default_orders']))
				return json_decode($info['default_orders'], true);
		}
		
		return json_decode($this->get('orders'), true);
	}	
	
	function getChilds($params=[], $max_level=3)
	{
		$menu = isset($params['menu']) ? $params['menu'] : true;
		$check_perm	= isset($params['check_permissions']) ? $params['check_permissions'] : true;			
		$pid = isset($params['parent_id']) ? $params['parent_id'] : 0;
		
		$nosep = isset($params['return_first_only']) ? ' AND path!="separator"' : '';
		$inmenu = ($menu?' AND active AND in_menu':'');
		$cond = ['parent_id=?'.$inmenu.$nosep, $pid];
		
		
		if(isset($params['test']))
			d::dumpas($params);
			

		$list_0 = $this->findAll($cond);
		$list = Array();
		
		foreach($list_0 as $i => $item)
		{		
			$can_access = isset($params['can_access']) ? call_user_func($params['can_access'], $item) : true;
			
			if($max_level<0)
				continue;
			
			if(!$check_perm || $can_access || $item->getChilds(['parent_id'=>$item->id]+$params, $max_level-1))
			{
				if(isset($params['return_first_only']) && $params['return_first_only']) 
					return $item;
				
						
				$list[] = $item; 
			}
		}
			
		return $list;
	}

	
	/* uzloadinamas puslapis taip pat prideedamas data_object_id*/
	
	function getByPath($path)
	{		
		
		//tam atvejui kai path eina gylin pvz straipsniumodulis/grupes/1/tagai/1
		//tuo atveju ieskoma puslapiu su pathais straipsniumodulis/grupes/tagai ir straipsniumodulis/tagai 
		
		preg_match_all('/([^\/]+)(\/\d{1,20})?/', $path, $matches, PREG_SET_ORDER);
		
		if(count($matches) > 2)
			$path_clean = implode('/',[$matches[0][1], $matches[count($matches)-1][1]]);
		
		
		if($data_object_id=(int)pathinfo($path,PATHINFO_FILENAME))
			$path=dirname($path);
		
			
		//isvalyt nuo identifikatoriu
		$path = preg_replace('/\/\d+\//','/',$path);
		
		$path_cond = isset($path_clean) ? GW_DB::inConditionStr("path", [$path, $path_clean]) : ['path=?', $path];
		
		
		if(($tmp = $this->find($path_cond)) && $data_object_id)
			$tmp->set('data_object_id', $data_object_id);
			
		return $tmp;
	}
	

	
	function deleteChilds()
	{
		$list = $this->findAll("path LIKE '".GW_DB::escape($this->path)."/%'");
		
		foreach($list as $item)
			$item->delete();
	}

	function eventHandler($event, &$context_data=[])
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
				GW_Permissions::deleteByPath($this->get('path'));
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}
	
	function getDataObject()
	{
		if($cache =& $this->cache['data_object'])
			return $cache;
		
		$info=$this->get('info');
		
				
		if(!isset($info->model) || !$Class=$info->model)
			return false;
			
			
		$class=strtolower($Class);
		$class=str_replace(['/','\\'],'',$class);
		
		list($dir) = explode('/', $this->get('path'));
		$dir=GW::s('DIR/ADMIN/MODULES').$dir.'/';
		
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
	
	
	function getIcon()
	{
		if($this->info->icon){
			return $this->info->icon;
		}
		if($this->info->iconclass){
			return "<i class='{$this->info->iconclass}'></i>";
		}	
	}
	
	
	
}
