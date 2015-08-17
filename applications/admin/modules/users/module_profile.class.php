<?php


class Module_Profile extends GW_Module
{	
	function init()
	{
		$this->model = new GW_User();
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->app->user];		
	}

	function doSave()
	{
		$this->viewDefault();
	}
	
	function viewLogout()
	{
		$this->app->auth->logout();
		$this->app->jump(GW::s('ADMIN/PATH_LOGIN'));
	}
	
	function doUpdateMyPass()
	{
		$vals=$_REQUEST['item'];
		
		$item =& $this->app->user;
		$item->setValues($vals);		
		
		$item->setValidators('change_pass_check_old');
		
		if(!$item->validate()){
			$this->setErrors($item->errors);
			$this->processView('default');
			exit;	
		}else{
			$item->setValidators(false);
			if($item->update(Array('pass')))
				$this->app->setMessage($this->lang['PASS_UPDATED']);
		}
		
		$this->jump();
	}
	
	function doUpdateMyProfile()
	{
		$vals=$_REQUEST['item'];
		
		$fields=Array('name','surname','email');
		
		$item =& $this->app->user;
		$item->setValues($vals);	
		$item->setValidators('update');
			
		
		if(!$item->validate()){
			$this->setErrors($item->errors);
			
			$this->processView('default');
			exit;	
		}else{
			if($item->update($fields))
				$this->app->setMessage($this->smarty->_tpl_vars['lang']['UPDATE_SUCCESS']);
		}
		
		$this->jump();		
	}
	
	function doSwitchUserReturn()
	{
		$this->app->auth->switchUserReturn();
		$this->jump();
	}	
		
}

?>
