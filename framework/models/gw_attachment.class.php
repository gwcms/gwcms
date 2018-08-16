<?php

class GW_Attachment extends GW_i18n_Data_Object
{

	public $table = 'gw_attachments';
	public $i18n_fields = ['title'=>1];
	public $calculate_fields = [];
	//public $ignore_fields = ['fullkey'=>1];

	
	//'dimensions_min'=> '100x100'
	
	public $composite_map = [
		'image' => ['gw_image', ['dimensions_max'=>'10000x10000']],
		'file' => ['gw_file', []]
	];	
	
	public $encode_fields = [
	    'extra'=>'jsono',
	];	

	
	function setTitle($value, $ln='lt')
	{
		$this->set("title_{$ln}", $value);
	}
}
