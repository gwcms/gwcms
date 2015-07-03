<?php

class GW_Admin_Application extends GW_Application
{	
	function checkCompatability()
	{
		//NO MSIE 6
		if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6')!==false)
			die('MS Internet Explorer 6 not supported. <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx">Get new version</a>');


		//magic quotes should be off
		if($gpc = ini_get("magic_quotes_gpc"))
			trigger_error('echo "php_flag magic_quotes_gpc off" >> .htaccess', E_USER_ERROR);		
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
	
	function getPages($params=[])
	{
		$params['can_access']=[$this, 'canAccess'];
		
		$tmp = GW::getInstance('GW_ADM_Page')->getChilds($params);
		
		//d::dumpas($tmp);
		
		return $tmp;
	}
	
	function init()
	{
		parent::init();
		
		GW_ADM_Sitemap_Helper::updateSitemap();
		
	}
	
	
	function getBreadcrumbs()
	{		
		$list = $this->path_arr;
		
		foreach($list as $i => $item)
		{
			if(!isset($item['title']))
			{
				$page=GW::getInstance('GW_ADM_Page')->getByPath($item['path']);
				
				if($page)
				{
					$item['title_clean']=$page->title;
					$item['title'] = $item['title_clean'];
					
					if($do=$page->getDataObject()){
						$item['do_title'] = $do->title ? $do->title : $do->id;
						
						if($item['do_title'])
							$item['title'].=' ('.$item['do_title'].')';
					}
					
					
				}else{
					$item['title'] = $this->fh()->viewTitle($item['name']);
				}
			}
				
			if(!isset($item['title_clean']))
				$item['title_clean']=$item['title'];
			
			
			$list[$i]=$item;
		}
		
		return $list;
	}
	
}