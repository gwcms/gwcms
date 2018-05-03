#!/usr/bin/php
<?php

chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php';


$id = $argv[1];

$scaffdir = GW::s('DIR/TEMP').'scaff_'.$id.'/';




if(is_dir($scaffdir)){

	
	$out = GW::fakerequest('admin/en/system/scaffold?act=doScaffoldProceed&scaffid='.$id, GW_USER_SYSTEM_ID);
	print_r($out);
	//file_put_contents('/tmp/test',$out);
	
}

