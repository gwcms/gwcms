<?php

class GW_QRcode_Tool
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
		//$this->app->initDB();
	}
	
	function process()
	{
		
		header('Content-type: image/png');
		$url = $_GET['code'];
		$img = shell_exec('qrencode --output=- -m=1 '.escapeshellarg($url));
		echo $img;

	}
}