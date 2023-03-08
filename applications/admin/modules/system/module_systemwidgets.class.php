<?php

class Module_SystemWidgets extends GW_Module
{

	public $is_widget_module = true;
	
	function init()
	{
		//$this->__processViewSolveViewName();
		//$this->db =& $this->app->db;
		//$this->tpl_dir="{$this->module_dir}tpl/".$this->module_name."/";

		//$this->smarty = $this->app->smarty;		
		
		
		parent::init();
	}
	
	function viewTestEnv()
	{		
		$this->tpl_vars['last_sync_time'] = file_get_contents(GW::s('DIR/TEMP').'sync_with_prod');
	}
	
	function doSyncWithProd() 
	{
		$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
		
		$msg = shell_exec($cmd="sudo /usr/bin/php $path test_sync_with_prod 2>&1");
		$this->setMessage($msg);
		header('Location: '.$_SERVER['HTTP_REFERER'] ?: '/');
		exit;
	}
}
