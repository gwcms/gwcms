<?php


class Module_Public extends GW_Common_Module
{	

	public $default_view = 'default';
	
	function init()
	{
		$this->model = new stdClass();
		parent::init();
	}

	

	
	

	function viewManifest()
	{	
		
		header('Content-type: application/json');
		
		$this->cfg = new GW_Config('sys/');
		
		echo json_encode([
			"name"=> GW::s('SITE_TITLE'),
			"short_name"=> GW::s('SITE_TITLE'),
			"icons"=> [[
				"src"=>$this->app->app_root.'static/img/logo_push_messages.png',
				'sizes'=>"192x192",
				'type'=>'image/png']],
			"start_url"=>$this->app->app_base,
			"display"=> "standalone",  
			"gcm_sender_id"	=>$this->cfg->google_project_id
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		
	
		exit;
	}
	
	function viewServiceWorker()
	{
		header('Content-type: application/javascript');
	}
	
	

}

