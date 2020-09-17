<?php


class GW_Forms extends GW_i18n_Data_Object
{	
	public $default_order="owner_type ASC, owner_field ASC";	
	public $i18n_fields = [
	    "title"=>1,
	    "ln_enabled"=>1
	];
	
	public $composite_map = [
		'elements' => ['gw_related_objecs', ['object'=>'GW_Form_Elements','relation_field'=>'owner_id', 'opts'=>['key_field'=>'fieldname','order'=>'priority ASC']]]
	];	

	
}			