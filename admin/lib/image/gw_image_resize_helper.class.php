<?php



class GW_Image_Resize_Helper
{
	/**
	 * 
	 * @param $image_obj GW_Image
	 * @param $params Array
	 * @return GW_Image
	 */
	

	
	function getCacheFileName($filename, $params)
	{
		return 
			GW::$dir['SYS_IMAGES_CACHE'].
			''.(int)$params['width'].'x'.(int)$params['height'].
			'__'.pathinfo($filename,PATHINFO_FILENAME).'__'.
			$params['method'].
			'.jpg';	// use jpg extension to all types
	}
	
	function formatResult(&$image_obj, $file)
	{
		
		$original = clone $image_obj;
		
		list($width, $height) = @getimagesize($file);
		
		$image_obj->set('width', $width);
		$image_obj->set('height', $height);
		$image_obj->set('size', @filesize($file));
		$image_obj->set('filename', basename($file));
		
		$image_obj->dir = GW::$dir['SYS_IMAGES_CACHE'];
		$image_obj->original = $original;
		
		return $item;
	}
	
	/**
	 * 
	 * @param $image_obj GW_Image
	 * @param $params
	 * @param $resize_format
	 * @return unknown_type
	 */
	
	function validateSaveFormats($str)
	{
		$formats=Array('jpg'=>1,'png'=>1,'gif'=>1);
		$default='jpg';
				
		return $formats[$str] ? $str : $default;
	}	
	
	function checkSaveFormat(&$params)
	{
		$params['save_format']=self::validateSaveFormats($params['save_format']);		
	}
	
	function resizeAndCache(&$item, $params)
	{
		self::checkSaveFormat($params);
		
		$destination = self::getCacheFileName($item->getFilename(), $params);

		if(file_exists($destination))
			return self::formatResult($item, $destination);		
		
		if(!self::resize($item, $params, $destination))
			return false;
		
	}
	
	function resize(&$item, $params, $destination)
	{
		self::checkSaveFormat($params);
		
		if(
			(int)$item->get('width') <= (int)$params['width'] && 
			(int)$item->get('height') <= (int)$params['height']
		)
		return false; // no need to resize
			
		$file = $item->getFilename();
		
		$im = new GW_Image_Manipulation($file);
		$im->resize($params);
		
		$params['save_format']='auto';
		
		$im->save($destination, $params['save_format']);
		$im->clean();
		
		if(!is_file($destination))
			trigger_error('Can not write to file "'.$destination.'"',E_USER_ERROR);
			
		
		self::formatResult($item, $destination);
			
		return true;
	}
	

	/**
	 * 
	 * @param $item GW_Image
	 */	 		
	function getCacheFiles($item)
	{
		$file = $item->original_file ? $item->original_file : $item->getFilename();
		
		return glob(GW::$dir['SYS_IMAGES_CACHE'].'*__'.pathinfo($file, PATHINFO_FILENAME).'__*');		
	}		
	
	/**
	 * 
	 * @param $image_obj GW_Image
	 */	 	
	function deleteCached(&$image_obj)
	{
		foreach(self::getCacheFiles($image_obj) as $file)
			unlink($file);
	}
}