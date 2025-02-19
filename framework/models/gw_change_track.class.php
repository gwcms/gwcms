<?php

class GW_Change_Track extends GW_Data_Object
{

	public $table = 'gw_change_track';

	//public $ignore_fields = ['fullkey'=>1];

	
	
	public $encode_fields = [
	    'new'=>'jsono',
	    'old'=>'jsono',
	    'diff'=>'jsono',
	];
}
