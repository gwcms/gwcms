<?php



class Module_Blocks extends GW_Common_Module
{	

	public $default_view = 'list';
	
	use Module_Import_Export_Trait;	
	
	function init()
	{
	
		parent::init();	
		$this->config = new GW_Config($this->module_path[0].'/');
		$this->config->preload('');
		
		$this->options['site_id'] = GW_Site::singleton()->getOptions($this->app->ln);
		
		
		$this->app->carry_params['site_id']=1;
		
		if(isset($_GET['site_id']))
		{
			$this->filters['site_id']=$_GET['site_id'];
		}elseif(GW::s('MULTISITE') && $this->config->blocks_filter_by_site){
			$this->filters['site_id'] = $this->app->site->id;
		}		
		
	}	
	
	
	function viewDefault()
	{
		
	}
	
	
	
	
	function getListConfig()
	{
		
		$cfg = parent::getListConfig();

		
		return $cfg;
	}	
	
	function __eventAfterForm()
	{
		$this->tpl_vars['form_width']="100%";
		$this->tpl_vars['width_title']="120px";
		
	}	
	
	
}
