<?php

class GW_Install_Helper
{
	function recursiveChmod($file, $files_mod=0666, $dirs_mod=0777, &$info) 
	{

		if (is_dir($file)) {
			 $handle = opendir($file);
			 while($filename = readdir($handle)) {
				 if ($filename != "." && $filename != "..") {
					 self::recursiveChmod($file."/".$filename, $files_mod, $dirs_mod, $info);
				 }
			 }
			closedir($handle);
	
			$info['dirs_chmod'][$file]=(int)@chmod($file,$dirs_mod);
			return;

		} else {
			$info['files_chmod'][$file]=(int)@chmod($file,$files_mod);
			return;
		}
	}
	
	function recursiveUnlink($file, &$info=false)
	{
		if (is_dir($file)) {
			 $handle = opendir($file);
			 while($filename = readdir($handle)) {
				 if ($filename != "." && $filename != "..") {
					 self::recursiveUnlink($file."/".$filename, $info);
				 }
			 }
			closedir($handle);

			$info['dirs_unlink'][$file]=(int)@rmdir($file);
		} else {
			$info['files_unlink'][$file]=(int)@unlink($file);
			return;
		}
	}
	
	function createDir($dir, $perm=0777)
	{
		$old = umask(0);
		mkdir($dir, $perm);
		umask($old);
	}

	
	
	function CheckFolders()
	{
		$info = Array();
		
		
		$folders_remove[] = GW::s('DIR/TEMPLATES_C');
		$folders_remove[] = GW::s('DIR/LANG_CACHE');
		
		foreach($folders_remove as $folder)
		{
			self::recursiveUnlink($folder, $info);
		}		
		
		
		$folders_create_0777 = GW::s('DIR/ADMIN');
		
		
		foreach($folders_create_0777 as $folder)
		{
			@self::createDir($folder, 0777);
			
			$info['folders_create'][$folder] = (int)is_dir($folder);
		}		
		
		
		$folders_777_666[] = GW::s('DIR/SYS_REPOSITORY');
		
		foreach($folders_777_666 as $folder)
		{
			 self::recursiveChmod($folder, 0666, 0777, $info);
		}
		
		return $info;

	}
}