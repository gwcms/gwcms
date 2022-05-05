<?php


class GW_Form_Answers extends GW_i18n_Data_Object
{
	
	public $ownerkey = 'forms/answers';	
	public $extensions = ['keyval'=>1, 'attachments'=>1];	
	public $composite_map = [
		'form' => ['gw_composite_linked', ['object'=>'GW_Forms','relation_field'=>'owner_id']],
		'user' => ['gw_composite_linked', ['object'=>'GW_User','relation_field'=>'user_id']],
		'doc' => ['gw_composite_linked', ['object'=>'GW_Doc','relation_field'=>'doc_id']],
	];		

	/*
	function getFiles()
	{
		return $this->extensions['attachments']->findAll();
	}
	*/

	
	/*
	public $composite_map = [
		'typeObj' => ['gw_composite_linked', ['object'=>'Shop_ProdTypes','relation_field'=>'type']],
		'parent' => ['gw_composite_linked', ['object'=>'Shop_Products','relation_field'=>'parent_id']],
		'image' => ['gw_image', ['dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100']],
	];	
	*/
	public $calculate_fields = [
		'obj'=>1, //susijes objektas pvz užsakymo dalis obj=gw_order_item~2734
	];	

	function calculateField($key)
	{
		switch ($key) {
			case "obj":
				$class = $this->obj_type;
				
				if(!$class)
					return false;
				
				if($class)				
					return $class::singleton()->createNewObject($this->obj_id, true);
			break;
						
		}
		
		return parent::calculateField($name);
	}
	
	

}