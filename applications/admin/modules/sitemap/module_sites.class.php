<?php



class Module_Sites extends GW_Common_Module
{	

	public $default_view = 'list';
	
	function init()
	{
		
		
		parent::init();	
	}	
	
	
	function viewDefault()
	{
		
	}
	
	
	
	
	function getListConfig()
	{
		
		//d::dumpas();
		
		$cfg = array('fields' => []);
		
		
						

		
		
		$cfg["fields"]["id"]="Lof";

		$cfg["fields"]["title"]="Lof";
		$cfg["fields"]["hosts"]="Lof";
		$cfg["fields"]["key"]="Lof";
		//$cfg["fields"]["admin_host"]="Lof";
					
		
		
		$cfg["fields"]['relations']='L';		
		$cfg["fields"]['update_time'] = 'lof';
		$cfg["fields"]['langs'] = 'Lof';
		$cfg["fields"]['priority'] = 'lof';
		$cfg["fields"]['timezone'] = 'Lof';
		$cfg["fields"]['favico'] = 'Lof';
		
		
		$cfg['inputs']['langs']=[
		    'type'=>'multiselect', 
		    'options'=>array_merge(GW::s('LANGS'),GW::s('i18nExt')),
		    'sorting'=>1, 'options_fix'=>1
		];
		
		$cfg['inputs']['key']=['type'=>'text'];
		$cfg['inputs']['hosts']=['type'=>'tags', 'placeholder'=>GW::l('/m/ADD_HOST')];
		$cfg['inputs']['title'] = ['type'=>'text', 'i18n'=>3, 'i18n_expand'=>1];

		
		$cfg['inputs']['timezone'] = ['type'=>'select_ajax', 'options'=>DateTimeZone::listIdentifiers(), 'empty_option'=>1, 'options_fix'=>1];
		$cfg['inputs']['favico'] = ['type'=>'image', 'hidden_note'=>"Min. rez. {$this->model->composite_map['favico'][1]['dimensions_min']}"];
		
		$this->tpl_vars['dl_output_filters']['favico'] = 'image_sm';

		
		return $cfg;
	}
	
	
}
