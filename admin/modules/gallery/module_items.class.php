<?php


class Module_Items extends GW_Common_Module_Tree_Data
{	
	
	function init()
	{
		parent::init();
		
		$this->config = $this->model->config();	
	}

	
	function viewDefault()
	{
		$this->viewList();
	}


	
	function viewImport()
	{

	}
    	
	function doUploadzip()
	{
		if(!$_FILES['zipfile']['tmp_name'] || !preg_match('/\.zip$/', $_FILES['zipfile']['name'])) 
		{
			$this->setError('/GENERAL/FAIL');
			$this->jump();
		}
		
		$zip_dir = GW::$dir['TEMP'].'gw_zipdir_'.rand(0, 9999).'/';
		GW_Install_Helper::createDir($zip_dir);
		
		chdir($zip_dir);
		exec($cmd='unzip -j ' . $_FILES['zipfile']['tmp_name'] . ' -d' . $zip_dir);	
		
		foreach(glob($zip_dir.'*') as $file)
		    @$this->importEntry($file);

		GW_Install_Helper::recursiveUnlink($zip_dir, $tmp);


    	$this->jumpAfterSave();			
	}	
	
	function importEntry($file)
	{
		$values['parent_id'] = $this->filters['parent_id'];	
    	$values['active']   = 0;
    	$values['type']=0;//image
    	$values['title']=pathinfo($file, PATHINFO_FILENAME);
    	
    	$image =Array
	    (
    		'new_file'=>$file,
    		'size'=>filesize($file),
    		'original_filename'=> pathinfo($file, PATHINFO_BASENAME),
    	);
    	
	    $item = $this->model->createNewObject($values);

	    $item->set('image', $image);
	   	$item->validate();		
        $item->insert();
	}
	
	function doSetPositions()
	{
		$positions = $_REQUEST['positions'];
		// Array(id=>relative_position,...);
		
		if(!count($positions))
			return false;

		reset($positions);
		$item=$this->model->find(Array('id=?', key($positions)));
		
		$this->model->savePositions($positions, $this->getMoveCondition($item));
		
		GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);	
		
		unset($_GET['positions']);
		$this->jump();
	}
	
	function doToggleListStyle()
	{
		$this->config->adm_list_style=(int)!$this->config->adm_list_style;
		$this->jump();
	}

}