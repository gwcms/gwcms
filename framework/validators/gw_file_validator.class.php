<?php

class GW_File_Validator Extends GW_Validator
{

	function __splitDimensionStr($string)
	{
		return explode('x', $string);
	}

	function isValidExtension($allowed)
	{
		$item = $this->validation_object;

		//pravaliduot extensiona

		$ext = strtolower(pathinfo($item->original_filename, PATHINFO_EXTENSION));

		return in_array($ext, $allowed);
	}

	function isValid()
	{
		$item = & $this->validation_object;
		$file_vali = & $item->validators['file'];

		$new_file = $item->get('new_file');

		//var_dump(file_exists($new_file));

		if (!file_exists($new_file))
			return ($item->errors[] = '/G/GENERAL/FILE/DOESNT_EXIST') && false;

		if (isset($file_vali['size_max']) && @filesize($new_file) > $file_vali['size_max'])
			return ($item->errors[] = '/G/GENERAL/FILE/TOO_LARGE') && false;

		if (isset($file_vali['allowed_extensions']) && !self::isValidExtension(explode(',', $file_vali['allowed_extensions'])))
			return ($item->errors[] = '/G/GENERAL/FILE_EXTENSION_NOT_ALLOWED') && false;
	}
}
