<?php
/**
 *
 * @author vidmantas
 *
 */




class GW_Page extends GW_i18n_Data_Object
{
	public $table = 'gw_sitemap';
	public $i18n_fields = Array('title'=>1, 'in_menu'=>1);
	public $default_order = 'priority ASC';
	public $calculate_fields = Array('child_count'=>1);
	public $level=0;

	function getChilds($params=Array())
	{		
		$id = $this->id ? (int)$this->id : -1;

		if(!isset($params['site_id']))
			$params['site_id'] = GW::s('MULTISITE') ? GW::$context->app->site->id : 0;

		$cond = ['parent_id=? AND site_id = ?'.($params['in_menu']?' AND active=1 AND in_menu_'.$this->lang():''), $id, $params['site_id'] ];		
		
		$list = $this->findAll($cond);
		
		if($params['return_first_only'])
		return $list[0];
			
		return $list;
	}


	function getByPath($path, $check_parent=false, $siteid=null)
	{
		$item0 = new GW_Page();
		
		if($siteid===null)
			$siteid = GW::s('MULTISITE') ? GW::$context->app->site->id : 0;
		
		while($path && strlen($path) > 0)
		{
			if($tmp = $item0->find(['path=? AND site_id=?',$path, $siteid]))
			return $tmp;

			if(!$check_parent)
				return false;

			$path = dirname($path);
				
			$i++;
		}

		return false; //no page found
	}
	
	function getByModulePath($path)
	{
		$list = $this->findAll("ca.path LIKE '$path'",
			[
				'select'=>'ca.path AS m_path, a.path AS p_path',
				'joins'=>[['inner','gw_templates AS ca','a.template_id=ca.id']],
				'return_simple'=>1,
				'assoc_fields'=>['m_path', 'p_path']
			]
		);
		
		return $list;	
	}
	
	//get page path by module path
	//gauti puslapio kelia pagal modulio kelia, salyga kad templeitas butu tik karta naudojamas
	function getSingleByModulePath($path)
	{
		$tmp = $this->getByModulePath($path);
		
		return isset($tmp[$path]) ? $tmp[$path] : false;
	}

	function getFirstChild()
	{
		if($item = $this->getChilds(Array('return_first_only'=>1)))
			return $item;
			
		return false;
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

	
	function fixUniqPathId($force=false, $recursiveInc=0)
	{
		if($force || !$this->unique_pathid)
		{
			$tmp = $this->pathname.($recursiveInc ? '-'.$recursiveInc : '');
			
			if($this->count(['id != ? AND unique_pathid = ?', $this->id, $tmp])){
				$this->fixUniqPathId(true, $recursiveInc+1);
			}else{
				$this->unique_pathid = $tmp;
			}
		}
	}
	
	function fixPath()
	{
		$parent=$this->getParent();

		$this->set('path', $path=($parent?$parent->get('path').'/':'').$this->get('pathname'));
		
	}

	function prepare()
	{
		if (!$this->pathname)
			$this->pathname = $this->title;

		$this->pathname = GW_Validation_Helper::pagePathName($this->pathname);
		$this->fixPath();
		$this->fixUniqPathId();
	}
	
	
	function eventHandler($event, &$context_data=[])
	{
		switch($event)
		{
			case 'AFTER_LOAD':
				$this->level = substr_count($this->get('path'), '/');
				break;
					
			case 'BEFORE_UPDATE':	
				if(isset($this->content_base['input_data']))
				{
					$this->saveContent($this->content_base['input_data']);
					unset($this->content_base['input_data']);
					unset($this->changed_fields['input_data']);
				}
			break;
			case 'BEFORE_INSERT':
				$this->prepare();
			break;
			
		}

		parent::eventHandler($event, $context_data);
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


	function getInputs()
	{
		if(! $tpl_id=$this->get('template_id'))
		return Array();

		$tplvar = new GW_TplVar();
		$list = $tplvar->findAll(Array('template_id=?', $tpl_id));

		return $list;
	}

	function getContent($key)
	{
		$cache =& $this->cache['input_data'];

		if($cache)
			return $cache[$key];
			

		$db = $this->getDB();
		$list = $db->fetch_rows("SELECT * FROM gw_sitemap_data WHERE page_id=".(int)$this->get('id')." AND ln='".addslashes($this->lang())."'");

		foreach($list as $inp_data)
			$cache[$inp_data['key']]=$inp_data['content'];
		
		//d::ldump([$key, $cache, $list]);

		return $cache[$key];
	}

	function saveContent($list)
	{
		$default = Array('page_id'=> (int)$this->get('id'));

		$vals=Array();

		foreach($list as $key => $value)
			$vals[] = $default + Array('key'=>$key, 'content'=>$value, 'ln'=>$this->_lang);

		$db =& $this->getDB();

		$db->delete("gw_sitemap_data", Array('page_id=? AND ln=?', (int)$this->get('id'), $this->_lang));

		$db->multi_insert('gw_sitemap_data', $vals);
	}

	function getTemplate()
	{
		if(!($id=(int)$this->get('template_id')))
		return false;

		$item = new GW_Template($id);
		$item->load();

		return $item;
	}
	
	function lang()
	{		
		if($this->_lang)
			return $this->_lang;
		
		
		return GW::$context->app->ln;
	}
	
	function addImageSettings()
	{
		foreach($this->getInputs() as $input){
			if($input->type=='image')
				$this->composite_map[$input->title] = Array('gw_image', $input->params);
		}
	}	
	
	function getImage($name)
	{
		if(!$this->composite_map)
			$this->addImageSettings();
			
		return $this->get($name);
	}

}
