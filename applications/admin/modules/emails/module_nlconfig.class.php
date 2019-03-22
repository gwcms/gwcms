<?php

class Module_NLConfig extends GW_Common_Module
{	
	public $default_view = 'default';
	
	function init()
	{
		$this->model = new GW_Config('newsletter/');
		
		parent::init();
	}
	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
	function __afterSave(&$vals)
	{
		//d::dumpas(GW::s('DIR/SYS_FILES').'.mail.key');
		file_put_contents(GW::s('DIR/SYS_FILES').'.mail.key', $vals['dkim_private_key']);
		chmod(GW::s('DIR/SYS_FILES').'.mail.key', 0600);
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
}
