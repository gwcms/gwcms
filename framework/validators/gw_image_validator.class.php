<?php

class GW_Image_Validator Extends GW_Validator
{

	function __splitDimensionStr($string)
	{
		return explode('x', $string);
	}

	function isValidDimensions($min_dim = false, $max_dim = false)
	{
		$w = $this->validation_object->get('width');
		$h = $this->validation_object->get('height');

		if ($min_dim) {
			list($w_min, $h_min) = self::__splitDimensionStr($min_dim);
			if ($w < $w_min || $h < $h_min)
				return false;
		}

		if ($max_dim) {
			list($w_max, $h_max) = self::__splitDimensionStr($max_dim);
			if ($w > $w_max || $h > $h_max)
				return false;
		}

		return true;
	}

	function isValid()
	{
		$item = & $this->validation_object;
		$im_vali = & $item->validators['image_file'];

		$new_file = $item->get('new_file');

		//var_dump(file_exists($new_file));

		if (!file_exists($new_file))
			return ($item->errors[] = '/G/GENERAL/FILE/DOESNT_EXIST') && false;

		if (isset($im_vali['size_max']) && @filesize($new_file) > $im_vali['size_max'])
			return ($item->errors[] = '/G/GENERAL/FILE/TOO_LARGE') && false;

		if (isset($im_vali['dimensions_min']) && !self::isValidDimensions($im_vali['dimensions_min']))
			return ($item->errors[] = '/G/GENERAL/IMAGE/ERR_DIMENSIONS_MIN') && false;

		if (isset($im_vali['dimensions_max']) && !self::isValidDimensions(false, $im_vali['dimensions_max']))
			return ($item->errors[] = '/G/GENERAL/IMAGE/ERR_DIMENSIONS_MAX') && false;
	}
}
