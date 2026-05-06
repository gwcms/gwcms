<?php

use Minishlink\WebPush\VAPID;

class Module_Cfg extends GW_Module_Config_Common
{	
	function init()
	{
		$this->model = new GW_Config('');
		
		parent::init();
	}

	function __afterSave(&$vals)
	{
		//;
	}

	// Sugeneruoja naują VAPID public/private raktų porą ir išsaugo ją sistemos konfige.
	function doGenerateVapid()
	{
		if($this->model->get('sys/VAPID_PUBLIC_KEY')){
			return $this->setError('Not allowed to generate please remove/clear existing');
		}
		
		$keys = VAPID::createVapidKeys();

		
		
		$this->model->set('sys/VAPID_PUBLIC_KEY', $keys['publicKey']);
		$this->model->set('sys/VAPID_PRIVATE_KEY', $keys['privateKey']);

		$this->setPlainMessage('VAPID keys generated');
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
