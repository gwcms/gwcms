<?php


class GW_Article extends GW_Composite_Data_Object
{
	var $table = 'gw_articles';
	
	var $composite_map = Array
	(
		'image' => Array('gw_image', Array('dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100')),
	);
	
	
}