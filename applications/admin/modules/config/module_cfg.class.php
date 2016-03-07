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
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);
		
		
		
		$this->__afterSave($vals);
		
		
		$this->jump();
	}

	function viewManifest()
	{
		echo '{  
  "name": "Push Demo",  
  "short_name": "Push Demo",  
  "icons": [{  
        "src": "images/icon-192x192.png",  
        "sizes": "192x192",
        "type": "image/png"
      }],  
  "start_url": "/test/index.html?homescreen=1",  
  "display": "standalone",  
  "gcm_sender_id": "'.$this->model->google_project_id.'"
}';
		exit;
	}
	
	

}

?>
