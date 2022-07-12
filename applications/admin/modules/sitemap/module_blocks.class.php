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
	

	
	function getListConfig($item=false)
	{
		
		$cfg = parent::getListConfig();

		
		if(GW::s('MULTISITE')){
			$cfg['inputs']['site_id']=['type'=>'select', 'options'=>$this->options['site_id']];
		}
		

		
		$cfg['inputs']['name']=['type'=>'text'];	
		$cfg['inputs']['admnote']=['type'=>'text'];
		$cfg['inputs']['path_filter']=['type'=>'text'];
		
		$cfg['inputs']['contents_type']=['type'=>'select', 'options'=>GW::l('/m/OPTIONS/block_types')];
		
		$typemap = [1=>'text',2=>'textarea',3=>'htmlarea',4=>'code_smarty',5=>'text',6=>'color',7=>'text'];
		
		if($item){
			$cfg['inputs']['contents']=['type'=>$typemap[$item->contents_type]];
		}
		

		
		if($item && $item->contents_type==4){
			$cfg['inputs']['contents'] += isset($_GET['form_ajax']) ? 
				['height'=>"400px","width"=>'400px'] : 
				['height'=>"400px",'layout'=>'wide'];
		}
		
		
		
		$cfg['inputs']['preload']=['type'=>'select', 'options'=>GW::l('/m/OPTIONS/preload')];
		$cfg['inputs']['active']=['type'=>'bool'];
		$cfg['inputs']['ln']=['type'=>'select', 'options'=>array_merge(['*'=>GW::l('/m/ALL_LANGS')],GW::s("LANGS"))];
		
		
		$cfg['filters']['site_id'] = ['type'=>'multiselect','options'=>$this->options['site_id']];

		//d::dumpas($cfg['inputs']);

		
		return $cfg;
	}	
	

		
			
	
	
	function __eventAfterForm()
	{
		$this->tpl_vars['form_width']="100%";
		$this->tpl_vars['width_title']="120px";
		
	}	
	
	
}
