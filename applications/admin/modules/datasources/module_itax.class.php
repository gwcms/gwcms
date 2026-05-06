<?php



class Module_Itax extends GW_Module_Config_Common
{	
	/**
	 *
	 * @var Itax 
	 */
	public $model;
	
	function init()
	{		
		
		$this->lgr = new GW_Logger(GW::s('DIR/LOGS').'itax.log');
		$this->lgr->collect_messages = true;
		
		$this->config = new GW_Config($this->module_path[0].'/');
		//$this->model = new Itax($this->config->itax_apikey);
		$this->model = $this->config;
		
		parent::init();
	}
	
	function viewOptionsRemote()
	{
		$args = $_GET;
		unset($args['url']);		
				
		$resp = Menuturas_Api::singleton()->request('itax/itax/optionsajax', $args, [], $_POST ?? []);

		die($resp);		
	}
	
	protected function notifyConfigSaveSuccess()
	{
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
	}
	
	
}




