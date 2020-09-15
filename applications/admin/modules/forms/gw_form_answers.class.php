<?php


class GW_Form_Answers extends GW_Data_Object
{
	
	public $ownerkey = 'forms/answers';	
	public $extensions = ['keyval'=>1, 'attachments'=>1];	
	

	/*
	function getFiles()
	{
		return $this->extensions['attachments']->findAll();
	}
	*/
	
	public $calculate_fields = [
	    'keyval' => 1
	];
	public $ignore_fields = [
	    'keyval' => 1
	];	
	
	/*
	public $composite_map = [
		'typeObj' => ['gw_composite_linked', ['object'=>'Shop_ProdTypes','relation_field'=>'type']],
		'parent' => ['gw_composite_linked', ['object'=>'Shop_Products','relation_field'=>'parent_id']],
		'image' => ['gw_image', ['dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100']],
	];	
	*/
	

	function calculateField($key)
	{
		switch ($key) {
			case 'keyval':
				return $this->extensions['keyval'];
						
		}
		
		return parent::calculateField($name);
	}
	
	

}