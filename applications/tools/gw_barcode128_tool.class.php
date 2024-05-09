<?php

class GW_BarCode128_Tool
{
	public $path_arr;
	
	public $admin=false;
	
	public $app;
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	
	function init()
	{
		$this->app->initDB();
	}
	
	function process()
	{
		
		set_include_path(GW::s('DIR/PEAR'));
		require_once GW::s('DIR/PEAR').'Image/Barcode.php';
		$imbc = new Image_Barcode;
		$imbc->draw($_REQUEST['code'], 'code128', 'png');
	}
}