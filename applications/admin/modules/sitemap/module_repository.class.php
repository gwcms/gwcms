<?php

class Module_Repository extends GW_Common_Module 
{

	
	function init()
	{	
		parent::init();

		$this->model = new stdClass();
	

	
		
		$this->app->carry_params['clean']=1;
	}
	
	

	
	function viewFileSelect()
	{
		
		
		
	}
	
	
	function isImage($filename)
	{
		return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg','jpeg','png','gif']);
	}
	
	function viewFilesList()
	{
		$dir = $_GET['dir'];			
		$dir = str_replace('../', '', $dir);
		$root=GW::s('DIR/REPOSITORY');
		$files0 = glob($root.$dir.'*');
		
		$files = [];
		$dirs = [];
		
		
		usort( $files0, function( $a, $b ) { return filemtime($a) - filemtime($b); } );
		
		foreach($files0 as $idx =>  $file){
			$relative = str_replace($root,'',$file);
			
			if(is_dir($file))
				$dirs[$file] = $relative;
			else
				$files[$file] = $relative;
		}
		
		
		
		
		if(isset($_GET['ftype']) && $_GET['ftype']=='image')
			foreach($files as $idx => $file)
				if(!$this->isImage($file))
					unset($files[$idx]);	
				
				
				
		
		$this->tpl_vars['dirs'] = $dirs;
		$this->tpl_vars['files'] = $files;
	}
	
	
	function doUpload()
	{
		$files = GW_File_Helper::reorderFilesArray('files');
		
		$dir = $_GET['dir'];			
		$dir =  GW::s('DIR/REPOSITORY').str_replace('../', '', $dir);
		
		if(!is_dir($dir))
			die('bad dir');
		
		foreach($files as $file)
		{
			$name = GW_File_Helper::cleanName($file['name']);
			
			copy($file['tmp_name'], $dir.'/'.$name);
		}
		
		die('OK');
	}
	
	
}
