<?php


class GW_Movie extends GW_Composite_Data_Object
{
	var $table = 'movies';
	
	
	var $composite_map = Array
	(
		'image1' => Array('gw_image', Array('dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100')),
	);
	
	
}