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
	
}
