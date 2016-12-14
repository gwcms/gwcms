<?php


class GW_Movie extends GW_Composite_Data_Object
{
	var $table = 'movies';
	
	
	var $composite_map = Array
	(
		'image1' => Array('gw_image', Array('dimensions_resize'=>'200x200', 'dimensions_min'=> '100x100')),
	);
	
	
}