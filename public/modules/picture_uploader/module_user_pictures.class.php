<?php

include GW::$dir['PUB_LIB'].'gw_public_module.class.php';

class Module_User_Pictures extends GW_Public_Module
{
	
	function init()
	{
		include GW::$dir['PUB_MODULES'].'picture_uploader/user_picture.class.php';
		
		$this->model = new User_Picture();
		//dump('init check');
	}
	
	
	function viewDefault()
	{
		//backtrace();
		//dump('viewDefault');
		//$this->viewList();
	}
	
	function viewList()
	{
		$options=Array('dump'=>'dump');
		$list = $this->model->findAll();
		$this->smarty->assign('list', $list);
	}
	
	function doSave()
	{
		$item = new User_Picture();
		
		if(count($_FILES)){
			$item->id = time() . (rand(0, 1000));
			GW_Image_Helper::__setFiles($item);
			
			//$this->fireEvent('BEFORE_SAVE', $item);
			$item->saveCompositeItems();
			echo $item->image->key;
			exit;	
		}
		echo "Error";
			
	}
	
	function doDelete()
	{
		
	}
	
}