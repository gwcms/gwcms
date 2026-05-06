<?php

class Module_NLConfig extends GW_Module_Config_Common
{	
	function init()
	{
		$this->model = new GW_Config('newsletter/');
		
		parent::init();
	}
	function __afterSave(&$vals)
	{
		//d::dumpas(GW::s('DIR/SYS_FILES').'.mail.key');
		file_put_contents(GW::s('DIR/SYS_FILES').'.mail.key', $vals['dkim_private_key']);
		chmod(GW::s('DIR/SYS_FILES').'.mail.key', 0600);
	}
}
