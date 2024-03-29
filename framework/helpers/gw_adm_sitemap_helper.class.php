<?php

class GW_ADM_Sitemap_Helper
{

	static function __listModules($search = '*')
	{
		return glob(GW::s('DIR/ADMIN/MODULES') . $search . '/lang.xml');
	}

	static function listModules()
	{
		foreach (self::__listModules() as $mapfile) {
			$id = basename(dirname($mapfile));
			$list[$id] = $mapfile;
		}

		return $list;
	}
	
	
	static function arrayToTree(&$list)
	{
		$childs = [];
		foreach($list as $idx =>  $item)
		{
			
			if($item->parent_id!=0)
			{
				$childs[$item->parent_id][] = $item;
				unset($list[$idx]);
			}
			

		}	

		$newlist=[];

		foreach($list as $item){
			$newlist[] =$item;
			
			if(isset($childs[$item->id])){
				$item->__child_count=count($childs[$item->id]);
				$newlist = array_merge($newlist, $childs[$item->id]);
			}
		}
		$list = $newlist;		
	}

	static function treeToArray(&$tree)
	{
		$rez = Array();
		self::__treeToArray($tree, $rez, '');

		return $rez;
	}

	static function __treeToArray(&$tree, &$array, $path)
	{
		$item = $tree;
		unset($item['childs']);
		$array[$path] = $item;

		if (isset($tree['childs']) && $tree['childs'])
			foreach ($tree['childs'] as $key => $child)
				self::__treeToArray($child, $array, $path . '/' . $key);
	}

	static function loadModuleMap($pathname)
	{
		$tmp = GW_Lang_XML::getAllLn(GW::s('ADMIN/LANGS'), $path = GW::s('DIR/ADMIN/MODULES') . $pathname . '/lang.xml');

		foreach ($tmp as $ln => $tree) {
			if ($tree['MAP'] ?? false)
				$map[$ln] = self::treeToArray($tree['MAP']);
		}
		
		if(!isset($map))
			GW::$context->app->setError('Having problems reading '.$pathname.'/lang.xml');

		return $map ?? [];
	}

	static function syncModule($pathname)
	{
		if (!$all_ln_tree = self::loadModuleMap($pathname))
			return false;

		$list = Array();

		$priority = 1;
		$page0 = GW::getInstance('GW_ADM_Page');

		
		
		
		foreach ($all_ln_tree as $ln => $tree) {
			foreach ($tree as $path => $values) {
				$path = $pathname . $path;

				if (!($item = @$list[$path]))
					if (!($item = $page0->getByPath($path))) {
						$item = new GW_ADM_Page();
						$values['path'] = $path;
						$values['pathname'] = basename($path);
					}

				if (strpos($path, '/') !== false) // dont set priority for root pages
					$values['priority'] = $priority++;
				$values['sync_time'] = date('Y-m-d H:i:s');
				
				$item->setValues($values, $ln);
				$list[$path] = $item;
			}
		}

		foreach ($list as $item)
			$item->save();

		//delete old items-------------------	
		$item0 = new GW_ADM_Page();
		$old_list = $item0->findAll('path LIKE "' . GW_DB::escape($pathname) . '/%"');


		foreach ($old_list as $item)
			if (!isset($list[$item->get('path')]))
				$item->delete();
		//-----------------------------------
	}

	static function updateSitemap($force = false)
	{
		$page0 = new GW_ADM_Page;
		$root_pages = $page0->getChilds(Array('check_permissions' => false, 'menu' => false));

		$modules = self::listModules();

		//check sync
		if (!$force)
			foreach ($root_pages as $i => $item)
				if (date('Y-m-d H:i:s', @filemtime($modules[$item->get('path')] ?? false)) <= $item->get('sync_time'))
					unset($modules[$item->get('path')]);


		//testing sync
		//$modules['sitemap']=1;

		if (!$modules)
			return;

		$msg = "Synchronized modules: " . implode(',', array_keys($modules));

		foreach ($modules as $pathname => $langfile)
			self::syncModule($pathname);


		self::updateParentIds();

		return [$msg];
	}

	static function updateParentIds($force = false)
	{
		$page = new GW_ADM_Page();
		$cond = $force ? '' : 'parent_id = -1';
		$list = $page->findAll($cond);

		$db = $page->getDB();

		foreach ($list as $item) {
			$sql = Array("SELECT id FROM $page->table WHERE path = ?", dirname($item->get('path')));

			if (!$parent_id = $db->fetch_result($sql))
				$parent_id = 0;

			$item->set('parent_id', $parent_id);
			$item->update(Array('parent_id'));
		}
	}
}
