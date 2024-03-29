<?php
	
ini_set('memory_limit', '300M');
ini_set('upload_max_filesize', '300M');
ini_set('post_max_size', '300M');
set_time_limit(500);

class Module_Items extends GW_Common_Module_Tree_Data
{	
	
	function init()
	{
		parent::init();
		
		$this->config = $this->model->config();	
		
		if($this->app->site->id == 1){
			$this->options['site_id'] = GW_Site::singleton()->getOptions();
		}
		

	}

	function __eventAfterListParams(&$params)
	{		
		//
	
		if($this->app->site->id != 1){
			$params['conditions'] = ($params['conditions'] ? '('.$params['conditions'].') AND':'').' (site_id='.(int)$this->app->site->id.' OR `type`='.GW_GALLERY_ITEM_IMAGE.')';
		}
		
					

		
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
			$this->setError('/G/GENERAL/FAIL');
			$this->jump();
		}
		
		$zip_dir = GW::s('DIR/TEMP').'gw_zipdir_'.rand(0, 9999).'/';
		GW_Install_Helper::createDir($zip_dir);
		
		chdir($zip_dir);
		exec($cmd='unzip -j ' . $_FILES['zipfile']['tmp_name'] . ' -d' . $zip_dir);	
		
		foreach(glob($zip_dir.'*') as $idx => $file)
		    @$this->importEntry($file, pathinfo($file, PATHINFO_BASENAME), isset($_POST['activate']), $idx);

		GW_Install_Helper::recursiveUnlink($zip_dir, $tmp);
		
		$this->jumpAfterSave();	
	}
	

	
	function doUploadMultiple()
	{
		
		$files = GW_File_Helper::reorderFilesArray('multiple_files');
		
		foreach($files as $idx => $file)
		{
			$lastitem=$this->importEntry($file['tmp_name'], $file['name'], isset($_POST['activate']), $idx);
		}
		
		$lastitem->fixOrder();
		
		$this->jumpAfterSave();	
	}
	
	function importEntry($file, $orig_filename, $active, $idx) 
	{
		
		$values['parent_id'] = $this->filters['parent_id'];
		$values['active'] = $active ? 1 : 0;
		$values['type'] = 0; //image
		$values['title'] = $orig_filename;
		$values['priority'] = 0-$idx-1;

		$image = Array
		    (
		    'new_file' => $file,
		    'size' => filesize($file),
		    'original_filename' => $orig_filename,
		);

		$item = $this->model->createNewObject($values);

		$item->set('image', $image);
		$item->validate();
		$item->insert();
		
		return $item;
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
		
		$this->setPlainMessage('/g/SAVE_SUCCESS');	
		
		unset($_GET['positions']);
		$this->jump();
	}
	
	function doToggleListStyle()
	{
		$this->config->adm_list_style=(int)!$this->config->adm_list_style;
		$this->jump();
	}

	
	function getOptionsCfg()
	{
		$typedir=GW_GALLERY_ITEM_FOLDER;
		return ['condition_add'=>"`type` = $typedir"];
	}
}
