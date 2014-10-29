<?php


class Module_Tools extends GW_Module
{	

	function init()
	{
		parent::init();
	}

	
	function viewDefault()
	{

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
