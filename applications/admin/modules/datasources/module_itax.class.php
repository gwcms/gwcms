<?php



class Module_Itax extends GW_Common_Module
{	

	public $default_view = 'default';
	
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
	
	

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}


	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		foreach($vals as $key => $val)
			if(is_array($val))
				$vals[$key] = json_encode($val);
		
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}	
	
	
}




