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
		
		
		$cfg = parent::getListConfig();
		
		
						

		
		
		$cfg["fields"]["id"]="Lof";

		$cfg["fields"]["title"]="Lof";				
		
		$cfg["fields"]['relations']='L';
		$cfg["fields"]['favico']='L';
		$cfg["fields"]['keyval/short_url']='L';
		

		
		
		
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
		
		$cfg['inputs']['ln_by_geoip_map'] = ['type'=>'code_json', 'hidden_note'=>'Exmpl: {"LT":"lt","DE":"de","default":"en"}', 'hidden_note_copy'=>1, "height"=>"50px"];
		$cfg['inputs']['keyval/short_url'] = [
		    'type'=>'textarea', 'hidden_note'=>'pvz:<br>/promo1|/lt/a/straipsnai/15/promo1<br>/promo2|https://isorine.nuoroda.com/x/y/promo2', 
		    'hidden_note_copy'=>1, 
		    "height"=>"50px"];
		
		
		$this->tpl_vars['dl_output_filters']['favico'] = 'image_sm';

		
		return $cfg;
	}
	
	
}
//<br>/promo1|/lt/a/straipsnai/15/promo1<br>/promo2|https://isorine.nuoroda.com/x/y/promo2