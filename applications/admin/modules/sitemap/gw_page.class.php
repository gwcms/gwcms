<?php
/**
 *
 * @author vidmantas
 *
 */




class GW_Page extends GW_i18n_Data_Object
{
	public $table = 'gw_sitemap';
	public $i18n_fields = ['title'=>1, 'in_menu'=>1];
	public $default_order = 'priority ASC';
	public $calculate_fields = ['child_count'=>1, 'input_cfg'=>1];
	public $level=0;
	public $order_limit_fields = ['parent_id'];
	
	
	
	public $keyval_use_generic_table=1;
	public $ownerkey = 'sitemap/pages';
	
	public $extensions = [
	    'keyval'=>1,
	    'changetrack'=>1
	];
		
	function getChilds($params=Array())
	{		
		$id = $this->id ? (int)$this->id : -1;

		if(!isset($params['site_id'])){
			if($this->site_id){
				$params['site_id'] = $this->site_id;
			}else{
				$params['site_id'] = GW::s('MULTISITE') ? GW::$context->app->site->id : 0;
			}
		}
		
		$params['in_menu'] = $params['in_menu'] ?? false;

		$cond = ['parent_id=? AND site_id = ?'.($params['in_menu']?' AND active=1 AND in_menu_'.$this->lang():''), $id, $params['site_id'] ];		
		
		$list = $this->findAll($cond);
		
		if($params['return_first_only'] ?? false)
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
			$ms_cnd = GW::s('MULTISITE') ? " AND (site_id=".(int)$siteid." OR multisite>0)" :'';
			$tmp = $item0->find(["path=? $ms_cnd",$path]);
			if($tmp)
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

	function pathnameExists($pathname)
	{
		$cond = [
			'id != ? AND parent_id = ? AND site_id = ? AND pathname = ?',
			(int)$this->id,
			(int)$this->parent_id,
			(int)$this->site_id,
			$pathname
		];

		return $this->count($cond) > 0;
	}

	function fixUniquePathname()
	{
		$base = $this->pathname ?: 'page';
		$pathname = $base;
		$idx = 1;

		while($this->pathnameExists($pathname)){
			$pathname = $base.'-'.$idx;
			$idx++;
		}

		$this->pathname = $pathname;

		if($pathname != $base && GW::$context->app)
			GW::$context->app->setMessage('Page path originally "'.$base.'" was replaced to "'.$pathname.'" cause "'.$base.'" was already in sitemap');
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
		$this->fixUniquePathname();
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
				
			break;
			case 'BEFORE_INSERT':
				$this->prepare();
			break;
			
			case 'BEFORE_DELETE':
				$this->deleteContent();
				
				foreach($this->getChilds() as $child)
					$child->delete();
			break;
			
		}

		parent::eventHandler($event, $context_data);
	}

	function calculateField($key)
	{
		switch($key)
		{
			case 'child_count':
				return (int)$this->count(Array('parent_id=?',$this->get('id')));
			break;
			case 'input_cfg':
				return $this->getInputs();
			break;
		}
	}


	function getInputs()
	{
		if(! $tpl_id=$this->get('template_id'))
			return [];

		if(isset($this->cache['template_inputs']))
			return $this->cache['template_inputs'];

		$list = GW_TplVar::singleton()->findAll(['template_id=?', $tpl_id], ['key_field'=>'name', 'order'=>'priority ASC']);

		return $this->cache['template_inputs'] = $list;
	}

	
	function getContentJsonDecode($key, $ln=false)
	{
		return json_decode($this->getContent($key, $ln), true);
	}
	
	
	function __getContent($key=null, $ln=false)
	{
		if(!isset($this->cache['input_data'])){
			$c =& $this->cache['input_data'];
			
			$list = $this->getDB()->fetch_rows("SELECT * FROM gw_sitemap_data WHERE page_id=".(int)$this->get('id'));

			foreach($list as $val){
				$field = $val['ln'] ? $val['key'].'_'.$val['ln'] : $val['key'];
				
				$this->cache['input_data'][$field] = $val['content'];
			}
		}
		$c =& $this->cache['input_data'];
		
		if(!$ln)
			$ln = $this->lang();
		
		
		//d::ldump([$key, $ln,$c]);
		if($key===null)
			return $c;
		
		if(isset($c[$key]))
			return $c[$key];
		
		if(isset($c["{$key}_{$ln}"]))
			return $c["{$key}_{$ln}"];
			
		
		
		return $cache[$key] ?? null;
	}
	
	function getContent($key=null, $ln=false, $decorate=true)
	{
		$val = $this->__getContent($key, $ln);

		if($decorate && $key !== null && $this->isFrontendContentEditingEnabled()){
			$input = $this->getInputs()[$key] ?? null;
			$type = $input ? $input->get('type') : '';
			$contentLn = $ln ?: $this->lang();
			$attrs = "data-pageid='".(int)$this->id."' data-parentid='".(int)$this->parent_id.
				"' data-siteid='".(int)$this->site_id."' data-contentkey='".htmlspecialchars($key, ENT_QUOTES).
				"' data-ln='".htmlspecialchars($contentLn, ENT_QUOTES)."'";

			if($type == 'text' || $type == 'textarea'){
				$instanceKey = $key.'/'.$contentLn;
				$instance = ($this->cache['frontend_content_instances'][$instanceKey] ?? 0) + 1;
				$this->cache['frontend_content_instances'][$instanceKey] = $instance;
				$identifier = 'pagecontent-'.(int)$this->id.'-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $key).'-'.preg_replace('/[^a-zA-Z0-9_-]/', '-', $contentLn).'-'.$instance;
				return "<span id='".$identifier."' class='pagecontent' ".$attrs.">".$val."</span>";
			}elseif($type == 'htmlarea' && !is_numeric($val) && self::__is_html($val)){
				$this->registerFrontendContentField($key, $ln, $val, $input, $type);
				return "<div class='ckedit' ".$attrs.">".$val."</div>";
			}
		}

		return $val;
	}

	static function __is_html($string)
	{
		return $string != strip_tags($string) || $string != html_entity_decode($string);
	}

	function getFrontendContentFields()
	{
		return array_values($this->cache['frontend_content_fields'] ?? []);
	}

	protected function registerFrontendContentField($key, $ln, $value, $input, $type)
	{
		if(!$ln)
			$ln = $this->lang();

		$value = is_scalar($value) ? (string)$value : '';

		$this->cache['frontend_content_fields'][$key.'/'.$ln] = [
			'pageid'=>(int)$this->id,
			'parentid'=>(int)$this->parent_id,
			'siteid'=>(int)$this->site_id,
			'key'=>$key,
			'ln'=>$ln,
			'type'=>$type,
			'value'=>$value,
			'multilang'=>(int)$input->get('multilang')
		];
	}

	function isFrontendContentEditingEnabled()
	{
		if(!GW::$context->app || GW::$context->app->app_name != 'SITE')
			return false;

		$user = GW::$context->app->user;
		if(GW::s('DEVELOPER_PRESENT') || ($user && ($user->is_admin || $user->isRoot())))
			return true;

		$adminUserId = $this->getFrontendAdminUserId();
		if(!$adminUserId)
			return false;

		$adminUser = GW_User::singleton()->find(['id=? AND active=1 AND banned=0', $adminUserId]);
		if(!$adminUser)
			return false;

		$adminSessionKey = GW::s('ADMIN/AUTH_SESSION_KEY') ?: 'cms_auth';
		$lastRequest = $_SESSION[$adminSessionKey]['last_request'] ?? -1;
		return $adminUser->isSessionNotExpired($lastRequest);
	}

	function getFrontendAdminUserId()
	{
		$adminSessionKey = GW::s('ADMIN/AUTH_SESSION_KEY') ?: 'cms_auth';
		return (int)($_SESSION[$adminSessionKey]['user_id'] ?? 0);
	}

	function exportContent($opts=[])
	{
		$extra = "";
		
		$lnscond = "";
		if($opts['lns'])
			$extra.=" AND ((".GW_DB::inConditionStr('ln', $opts['lns']).' ) OR ln="" ) ';
		
		$cond = "SELECT `ln`,`key`,`content` FROM gw_sitemap_data WHERE page_id=".(int)$this->get('id')." $extra";
				
		return $this->getDB()->fetch_rows($cond);
	}
	
	function searchContent($query)
	{
		
		$query = GW_DB::escape($query);
		$cond = "SELECT page_id FROM gw_sitemap_data WHERE content LIKE '%$query%'";
				
		return $this->getDB()->fetch_one_column($cond);		
	}
	
	function deleteContent()
	{
		return $this->getDB()->delete('gw_sitemap_data',"page_id=".(int)$this->get('id'));
	}

	function importContent($rows0)
	{
		$rows = [];
		
		foreach($rows0 as $row){
			$row = (array)$row;
			$row['page_id'] = $this->id;
			$row['update_time'] = date('Y-m-d H:i:s');
			$rows[] = $row;
		}
		
		$this->getDB()->multi_insert('gw_sitemap_data', $rows);
	}

	
	
	function getContentVersions($key=null, $ln=false, $count=1)
	{
		
		//$sel =  $count ? "count(*)"
		//if(!$ln)
		//	$ln = $this->lang();
			
	
		//return $this->getDB()->fetch_rows("SELECT uncompress(diff) FROM gw_sitemap_data_versions WHERE page_id=".(int)$this->get('id'));
		
		return $this->getDB()->fetch_assoc("SELECT concat(`key`,'/',ln),count(*)  FROM gw_sitemap_data_versions WHERE page_id=".(int)$this->get('id')." GROUP BY `key`, `ln`");
		
		
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
