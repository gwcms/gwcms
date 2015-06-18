<?php


class Module_Tools extends GW_Module
{	

	function init()
	{
		parent::init();
	}

	
	function viewDefault()
	{
		list($vars['lastupdates'], $vars['updatefiles']) = $this->__doImportSqlUpdates_list2update();
		

		return $vars;
	}
	

	function doInstall()
	{
		$this->log[] = GW_Install_Helper::CheckFolders();
	}
	
	function doDebugModeToggle()
	{
		$_SESSION['debug']=(int)$_SESSION['debug'];
		$_SESSION['debug']=($_SESSION['debug']+1) % 2;
		
		$this->jump();
	}
	
	
	
	
	function __executeQuery($sql)
	{
		
		$sqls = explode(';', $sql);
		
		$db =& $this->app->db;
		
		foreach($sqls as $sql)
		{
			if(!trim($sql))continue;
					
			$db->query($sql);
			$aff = $db->affected();

			$this->app->setMessage("<pre>".htmlspecialchars($sql).";\n<b># Affected rows:</b> ".$aff."</pre>");
		}
	}	
	
	
	
	function __doImportSqlUpdates_list2update()
	{
		$lastupdates= GW::getInstance('GW_Config')->get('gwcms/last_sql_updates');
		
		
		$list_files = glob(GW::s('DIR/ROOT').'sql/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]-[0-9]*.sql');
		
		$updates = [];
		
		foreach($list_files as $filename)
		{
			if(basename($filename) > $lastupdates)
				$updates[] = $filename;
		}
		
		return [$lastupdates, $updates];
	}
	
	function doImportSqlUpdates()
	{
		list($lastupdates, $updates) = $this->__doImportSqlUpdates_list2update();
		
		foreach($updates as $updatefile)
		{
			$sqls = file_get_contents($updatefile);
			$this->__executeQuery($sqls);
			
			GW::getInstance('GW_Config')->set('gwcms/last_sql_updates', basename($updatefile));
		}
		
		$this->jump();
	}
	
	
	function viewPHPinfo()
	{
		//
		phpinfo();
		
	}
	function viewCompatability()
	{
		$info = PHP_Info_Parser_Helper::parse();
		
		$gd_info=gd_info();
		$apache_loaded_modules=apache_get_modules();
		
		exec('unzip',$unzip);
		
		//satisfies
		$comp = Array
		(
			'phpversion'=>Array('required'=>'5.2.10','current'=>phpversion()),
			'magic_quotes'=>Array('required'=>0, 'current'=>ini_get('magic_quotes_gpc')),
			'short_open_tag'=>Array('required'=>1, 'current'=>ini_get('short_open_tag')),
			'mysql'=>Array('required'=>'5.1','current'=>$this->app->db->fetch_result('SELECT VERSION();')),
			'apache'=>Array('required'=>'2','current'=>$_SERVER['SERVER_SOFTWARE']),
			'gd'=>Array('required'=>'2','current'=>$gd_info['GD Version']),
			'apache_modules'=>Array
			(
				'mod_rewrite'=>Array('required'=>'1', 'current'=>(int)in_array('mod_rewrite', $apache_loaded_modules)),
			),
			'safe_mode'=>Array('current'=>(int)ini_get('safe_mode')),
			'upload_max_filesize'=>Array('current'=>ini_get('upload_max_filesize')),
			'post_max_size'=>Array('current'=>ini_get('post_max_size')),
			'exec unzip'=>Array('current'=> $unzip[0]),
			'admin/cli/system.php process id'=>GW_App_System::getRunningPid()
		);
		
		$this->log[]=$comp;
	}
	
	

	
}

?>
