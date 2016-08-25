<?php


class Module_Cfg extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->model = new GW_Config('sys/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
	
	
	function __afterSave(&$vals)
	{
		//;
	}
	
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

	function viewManifest()
	{
		echo json_encode([
			"name"=> GW::s('SITE_TITLE'),
			"short_name"=> GW::s('SITE_TITLE'),
			"icons"=> [[
				"src"=>$this->app->app_root.'img/logo/logo_with_ltr_color.png',
				'sizes'=>"192x192",
				'type'=>'image/png']],
			"start_url"=>$this->app->app_base,
			"display"=> "standalone",  
			"gcm_sender_id"	=>$this->model->google_project_id
		]);
		
	
		exit;
	}
	
	

}
