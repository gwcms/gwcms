<?php
class User_Picture extends GW_Composite_Data_Object
{
	
	var $composite_map = Array
	(
		'image' => Array('gw_image', Array('dimensions_resize'=>'9000x9000', 'dimensions_min'=> '100x100'))
	
	);
}