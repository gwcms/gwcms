<?php



class Module_Sellers extends GW_Common_Module
{	

	use Module_Import_Export_Trait;

	
	function init()
	{
		parent::init();	
		
		//$this->model = TS_Prises::singleton();
						
		
		$this->list_params['paging_enabled']=true;	
		

			
		
	}	
	
	
	
	
	
	function __eventAfterList($list)
	{

	}		
	
	

	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['image'] = "l";
		$cfg['fields']['ico'] = "l";
		$cfg['fields']['id'] = "lof";
		$cfg['fields']['insert_time'] = "lof";
		$cfg['fields']['update_time'] = "lof";
		$cfg['fields']['keyval/montonio_config'] = "L";	
		$cfg['fields']['keyval/paysera_config'] = "L";	
		
		//nerodys formoje
		$cfg['fields']['points_sngl'] = "lof";
		$cfg['fields']['points_dbl'] = "lof";
		$cfg['fields']['points_mx'] = "lof";
		$cfg['fields']['member_count'] = "lof";
		
		
		
		$cfg['inputs']['title']=['type'=>'text'];	
		$cfg['inputs']['approved']=['type'=>'bool'];	
		$cfg['inputs']['active']=['type'=>'bool'];	
		$cfg['inputs']['description']=['type'=>'textarea'];	
		$cfg['inputs']['website']=['type'=>'text'];	
		$cfg['inputs']['email']=['type'=>'text'];
		$cfg['inputs']['company_code']=['type'=>'text'];
		$cfg['inputs']['address']=['type'=>'text'];
		$cfg['inputs']['region']=['type'=>'text'];
		$cfg['inputs']['short']=['type'=>'text'];
		$cfg['inputs']['country']=['type'=>'select_ajax', 'modpath'=>"datasources/countries", 'empty_option'=>1,'options'=>[], "source_args"=>["byCode"=>1],'preload'=>1];
		
		
		
		
		

		$cfg['inputs']['user_id']=['type'=>'select_ajax', 'modpath'=>"users/usr", 'empty_option'=>1, 'options'=>[], 'preload'=>1];	
		
		$cfg['inputs']['keyval/montonio_config']=['type'=>'splittext', 'splitchar'=>'|', 'parts'=>2, 'width'=>'49%','hidden_note'=>'1st - access_key, 2nd - secret key'];	
		$cfg['inputs']['keyval/paysera_config']=['type'=>'splittext', 'splitchar'=>'|', 'parts'=>2, 'width'=>'49%','hidden_note'=>'1st - access_key, 2nd - secret key'];	
		
		

		
		
		return $cfg;
		
		//return ;
	}
	
	function getOptionsCfg()
	{
		return [
		    //'title_func'=>[TS_Organisers::singleton(),'titleInOptions'],
		    'search_fields'=>['title','short']
		];
	}	
	
	
	
}
