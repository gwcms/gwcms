<?php

class GW_Permissions
{

	static $table = 'gw_permissions';
	static $root_group_id = 1;
	static $cache = [];

	function __construct()
	{
		
	}

	/**
	 * 
	 * @return DB
	 */
	static function getDB()
	{
		return GW::$context->db;
	}

	static function save($group_id, $path_access)
	{
		$group_id = (int) $group_id;
		$db = self::getDB();

		$list = [];

		foreach ($path_access as $path => $access_level)
			$list[] = ['group_id' => $group_id, 'path' => $path, 'access_level' => $access_level];

		self::deleteAll($group_id);
		$db->_multi_insert(self::$table, $list);
	}

	static function deleteAll($group_id)
	{
		self::getDB()->delete(self::$table, ['group_id=?', $group_id]);
	}

	static function getByGroupId($group_id)
	{
		return self::getDB()->fetch_assoc("SELECT path,access_level FROM `" . self::$table . "` WHERE group_id=" . (int) $group_id);
	}

	
	static function testPermission($access_level, $requestAccess)
	{
		return $access_level & $requestAccess;
	}
	
	static function &__getPrmByMltGrpIds($gids, $path = false)
	{

		
		if (!count($gids)) {
			$empty = [];
			return $empty;
		}

		$sql = "SELECT path, max(access_level )FROM `" . self::$table . "` WHERE (";
		foreach ((array)$gids as $gid)
			$sql.= ' group_id=' . (int) $gid . ' OR ';

		$sql = substr($sql, 0, -4);
		$sql .=')';

		if ($path)
			$sql.=" AND path = '" . GW_DB::escape($path) . "'";

		$data = self::getDB()->fetch_assoc($sql. " GROUP BY path");
		
		return $data;
	}

	static function &getPrmByMltGrpIds($gids)
	{
		$cache_id = implode(',', (array) $gids);
		if ($cache_var = & self::$cache[$cache_id])
			return $cache_var;

		$cache_var = self::__getPrmByMltGrpIds($gids);
		return $cache_var;
	}

	
	/**
	 * Perdaryt kad grazintu access leveli
	 */
	static function canAccess($path, $gids, $load_once = true, $rootcheck=true)
	{
		if (self::isRoot($gids)){
			if($rootcheck){
				return true;
			}else{				
				return GW_ADM_Page::singleton()->count(['path=? AND active=1', $path]) > 0;
			}
		}
		
		if ($load_once)
			$paths = self::getPrmByMltGrpIds($gids);
		else
			$paths = self::__getPrmByMltGrpIds($gids, $path);
		
		if(isset($paths[$path]))
			return $paths[$path];
		
		//jei per permissionus uzdeta daugiau teisiu tai is permissionu ims
		
		$tmpacc = GW::$context->app->sess('temp_read_access');
		
		if(is_array($tmpacc) && ($tmpacc[$path] ?? false))
			return GW_PERM_READ;
	}
	
	static function setTempReadAccess($path, $ids)
	{
		$x = GW::$context->app->sess('temp_read_access');
		$x[$path] = $ids;
		GW::$context->app->sess('temp_read_access', $x);
	}
	

	
	static function getTempReadAccess($path)
	{
		$x = GW::$context->app->sess('temp_read_access');
		return $x[$path] ?? false;
	}		
	
	static function getTempReadAccessMod($path)
	{
		$x = GW::$context->app->sess('temp_read_access_mod');
		
		return $x[$path] ?? false;
	}	

	static function isRoot($gids)
	{
		if (in_array(self::$root_group_id, (array) $gids)) // root group has access to anything
			return true;
	}

	static function checkPages(&$list, $user)
	{
		foreach ($list as $i => $item)
			if (!self::canAccess($item->path, $user->group_ids))
				unset($list[$i]);
	}

	static function deleteByPath($path)
	{
		self::getDB()->delete(self::$table, ['path=?', $path]);
	}
}
