<?php

include_once __DIR__.'/module_genericcassificator.class.php';
class Module_prodtypes extends Module_GenericClassificator
{	
	
	
	function init()
	{
		parent::init();
		
		$fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', Shop_Products::singleton()->table]);	
		
		
		$opts = [];
		foreach($fields as $field)
			if($field->type=='optional')
				$opts[$field->fieldname] = $field->title;	
		
			
		
		$this->options['fields'] = $opts;
		
	}
}
