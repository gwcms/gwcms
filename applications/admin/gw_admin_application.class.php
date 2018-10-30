<?php

class GW_Admin_Application extends GW_Application
{	
	
	public $icon_root = 'static/img/icons/';

	function checkCompatability()
	{
		//NO MSIE 6
		if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6')!==false)
			die('MS Internet Explorer 6 not supported. <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">Get new version</a>');


		//magic quotes should be off
		if($gpc = ini_get("magic_quotes_gpc"))
			trigger_error('echo "php_flag magic_quotes_gpc off" >> .htaccess', E_USER_ERROR);		
	}
	
	
	function isEnabledPath($path)
	{		
		return GW_Permissions::canAccess($path, $this->user->group_ids, true, false);		
	}
	
	function canAccess($page)
	{
		if((bool)(int)$page->get('public'))
			return true;
		
		if(!$this->user)
			return false;
			
		if($this->user->isRoot())
			return true;
		
		return $page->get('active') && GW_Permissions::canAccess($page->get('path'), $this->user->group_ids);
	}
	
	function jumpToFirstChild()
	{
		if(!$item = $this->getPages(['return_first_only'=>1,'parent_id'=>$this->page->id]))
			$this->fatalError('Restricted access');
		
		
		$this->jump($item->get('path'));
	}	
	
	//gali buti ieskoma pvz
	//sitemap/templates/15/tplvars/form jei bus toks - sitemap/templates/tplvars tai supras
	//users/users/form 

	function getPage()
	{
		$this->page = new GW_ADM_Page();

		for ($i = count($this->path_arr) - 1; $i >= 0; $i--) {
			
			if ($tmp = $this->page->getByPath($this->path_arr[$i]['path_clean'])) {
				$this->page = & $tmp;
				return true;
			}
		}

		return false;
	}

	function getAdmPage($module_dirname, $modulename)
	{
		$this->page = new GW_ADM_Page();
				
		$path = $module_dirname.($modulename ? '/'.$modulename : '');
		
		//one level module path
		if($modulename && $modulename == $module_dirname){
			if($this->getAdmPage($module_dirname, false))
				return true;
		}

		if ($tmp = $this->page->getByPath($path) ) {
			$this->page = & $tmp;
			return true;
		}
	
		return false;		
	}
	
	
	
	function getPages($params=[])
	{
		$params['can_access']=[$this, 'canAccess'];
		
		$tmp = GW_Adm_Page::singleton()->getChilds($params);
		
		return $tmp;
	}
	
	function init()
	{
		parent::init();
		
		$this->autoPrepare();
		
	
		
		$this->icon_root = $this->app_root . $this->icon_root;
	}
	
		
	function getBreadcrumbs()
	{		
		$list = $this->path_arr;
		
		foreach($list as $i => $item)
		{
			if(!isset($item['title']))
			{
				$page= GW_ADM_Page::singleton()->getByPath($item['path']);
				
				if($page)
				{
					$item['title_clean']=$page->title;
					$item['title'] = $item['title_clean'];
					
					if($do=$page->getDataObject()){
						$item['do_title'] = $do->title ? $do->title : $do->id;
						
						if($item['do_title'])
							$item['title'].=' ('.$item['do_title'].')';
						
						if($page->info->itemactions)
						{
							$id = $do->id ? $do->id : $_GET['id'];
							$item['actions'] = $this->buildUri($item['path'].'/itemactions',['id'=>$id, 'RETURN_TO'=>$_SERVER['REQUEST_URI']]);
						}
					}
				}else{
					$item['title'] = GW::l('/A/VIEWS/'.$item['name']);
				}
			}
				
			if(!isset($item['title_clean']))
				$item['title_clean']=$item['title'];
			
			
			$list[$i]=$item;
		}
		
		return $list;
	}
	
	
	/**
	 * function is called on developer request
	 * to prepare system, exmpl: cache files
	 */
	function autoPrepare()
	{
		if(!$this->user || !$this->user->isRoot() || $this->user->id==GW_USER_SYSTEM_ID)
			return;
		
		
		GW::getInstance('GW_Config')->set('sys/project_url', Navigator::getBase(true));		
		
		//start system process
		if(GW::getInstance('GW_Config')->get('sys/autostart_system_process') && GW_App_System::startIfNotStarted())
		{
			$this->setMessage('System process was just started');
		}
	}
	
	
	function processHook($name)
	{
		$resore_module = GW_Lang::$module;
		
		if(is_array(GW::s("ADMIN/HOOKS/$name"))) {
			
			foreach(GW::s("ADMIN/HOOKS/$name") as $path){

				list($mod,$pathending) = explode('/',$path, 2);
				
				if($this->isEnabledPath($mod))
					$this->innerProcess($path);
			}
		}
		
		GW_Lang::$module  = $resore_module;
	}
	
	function process()
	{
		$path_info = $this->getModulePathInfo($this->path);
		$this->getAdmPage($path_info['dirname'], $path_info['module'] ?? $path_info['dirname']);
		
		if (!$this->canAccess($this->page)){			
			if ($this->user)
				$this->jumpToFirstChild();
			else
				$this->jump(GW::s("$this->app_name/PATH_LOGIN"));
		}
		
		$this->preRun();
		
		$this->processModule($path_info, $_REQUEST);
	}	
	
	function idInPath($id)
	{
		return is_numeric($id) ? $id : "id_".$id;
	}
	
	function innerRequest($path, $get_args, $post_args)
	{
				
		$get_args['GWSESSID']=session_id();
		$get_args['sys_call'] = 1;
		
		$path = $this->buildUri($path, $get_args, ['absolute'=>1]);
		
		
		$opts=[];
		
		if($post_args)
		{
			$opts = array('http' =>
			    array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($post_args)
			    )
			);	
			
			
		}
		
		$this->sessionWriteClose();
		$res = json_decode(file_get_contents($path, false, stream_context_create($opts)));
		$this->reopenSessionIfClosed();
		
		return $res;
	}
	
}
