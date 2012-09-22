<?php


include dirname(__DIR__).'/init.php';

function rglob($pattern='*', $flags = 0, $path='')
{
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    
    
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files,rglob($pattern, $flags, $path)); }
    
    return $files;
}


$list = rglob('.svn', 0, GW::$dir['ROOT']);


if($_SERVER['USER']!='root')
	die("cli root only \n");

if($argv[1]=='list')
	dump($list);

if($argv[1]=='delete')
	foreach($list as $item)
		GW_Install_Helper::recursiveUnlink($item);