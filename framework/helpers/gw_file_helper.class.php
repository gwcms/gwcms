<?php

class GW_File_Helper
{

	static function reorderFilesArray($name)
	{
		$files = array();
		foreach ($_FILES[$name]['name'] as $num_key => $dummy) {
			foreach ($_FILES[$name] as $txt_key => $dummy) {
				$files[$num_key][$txt_key] = $_FILES[$name][$txt_key][$num_key];
			}
		}

		return $files;
	}

	function createZip($files = array(), $destination = '', $overwrite = false)
	{
		//if the zip file already exists and overwrite is false, return false
		if (file_exists($destination) && !$overwrite) {
			return false;
		}
		//vars
		$valid_files = array();
		//if files were passed in...
		if (is_array($files)) {
			//cycle through each file
			foreach ($files as $file => $zippath) {
				//make sure the file exists
				if (file_exists($file)) {
					$valid_files[$file] = $zippath;
				}
			}
		}
		//if we have good files...
		if (count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach ($valid_files as $file => $zippath) {
				$zip->addFile($file, $zippath);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			//close the zip -- done!
			$zip->close();

			//check to make sure the file exists
			return file_exists($destination);
		} else {
			return false;
		}
	}

	function unlinkOldTempFiles($dir, $expire_time = '7 day')
	{
		$files = glob($dir . '/*');
		$expire_time = strtotime('-' . $expire_time);

		foreach ($files as $file) {
			if (filemtime($file) < $expire_time)
				unlink($file);
		}
	}

	function cleanName($name)
	{
		$name = str_replace(' ', '_', $name);
		setlocale(LC_CTYPE, 'en_GB.utf8');
		$name = iconv('UTF-8', 'us-ascii//TRANSLIT//IGNORE', $name);
		$name = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '_', $name);
		return $name;
	}

	function output($file)
	{
		header('HTTP/1.0 200 OK', true, 200);
		header("Content-Type: application/x-download");
		header('Content-Disposition: attachment; filename="' . basename($file) . '";');
		header("Accept-Ranges: bytes");
		header("Content-Length: " . filesize($file));


		$handle = fopen($file, 'rb');

		while (!feof($handle)) {
			echo fread($handle, 4096);

			ob_flush();
			flush();
		}

		fclose($handle);

		exit;
	}
	
	static function rglob($pattern, $flags = 0) 
	{
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
			$files = array_merge($files, self::rglob($dir . '/' . basename($pattern), $flags));
		}
		
		return $files;
	}
	
	static function isFilesEqual($filepath1, $filepath2) {

		$filesize1 = @filesize($filepath1);
		$filesize2 = @filesize($filepath2);

		if ($filesize1 != $filesize2)
			return false;

		if ($filesize1 === $filesize2) {

			$file1 = fopen($filepath1, 'r');
			$file2 = fopen($filepath2, 'r');

			for ($i = 0; $i < $filesize1 && $i < $filesize2; $i += 1) {
				fseek($file1, $i);
				fseek($file2, $i);
				if (fgetc($file1) !== fgetc($file2))
					return false;
			}

			fclose($file1);
			fclose($file2);

			return true;
		}
	}

}
