<?php


class Module_Profile extends GW_Module
{	
	function init()
	{
		$this->model = new GW_ADM_User();
		
		parent::init();
	}

	
	function viewDefault()
	{
		$this->smarty->assign('item',GW::$user);		
	}

	function doSave()
	{
		$this->viewDefault();
	}
	
	function viewLogout()
	{
		GW::$request->auth->logout();
		GW::$request->jump(GW::$static_conf['GW_SITE_PATH_LOGIN']);
	}
	
	function doUpdateMyPass()
	{
		$vals=$_REQUEST['item'];
		
		$item =& GW::$user;
		$item->setValues($vals);		
		
		$item->setValidators('change_pass_check_old');
		
		if(!$item->validate()){
			$this->setErrors($item->errors);
			$this->processView('default');
			exit;	
		}else{
			$item->setValidators(false);
			if($item->update(Array('pass')))
				GW::$request->setMessage($this->lang['PASS_UPDATED']);
		}
		
		$this->jump();
	}
	
	function doUpdateMyProfile()
	{
		$vals=$_REQUEST['item'];
		
		$fields=Array('name','email');
		
		$item =& GW::$user;
		$item->setValues($vals);	
		$item->setValidators('update');
			
		
		if(!$item->validate()){
			$this->setErrors($item->errors);
			
			$this->processView('default');
			exit;	
		}else{
			if($item->update($fields))
				GW::$request->setMessage($this->smarty->_tpl_vars['lang']['UPDATE_SUCCESS']);
		}
		
		$this->jump();		
	}
	
		
}

?>
