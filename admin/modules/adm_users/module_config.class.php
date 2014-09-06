<?php


class Module_Config extends GW_Common_Module
{	

	function init()
	{
		$this->model = new GW_Config('gw_adm_users/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		$this->smarty->assign('item', $this->model);
	}
	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);		
		
		$this->jump();
	}

}

?>
