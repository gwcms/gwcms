<?php


class Module_Attachments extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
	}
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
	
	
	
	
	function viewListAjax()
	{
		$owner_vals = [
		    'owner_type'=>($owner_type = $_GET['owner_type']), 
		    'owner_id'=>$_GET['owner_id'], 
		    'owner_temp_id'=>$_GET['owner_temp_id'],
		    'field'=>$_GET['field']
		];
		
		if($this->checkOwnerPermission($owner_vals['owner_type']))
			$list = GW_Attachment::singleton()->findAll(GW_DB::buidConditions($owner_vals), ['order'=>'priority ASC']);

		$this->tpl_vars['list'] = $list;
		
		
		$this->tpl_vars['validation'] = $this->getValidationParams($owner_type);
	}
	
	
	function checkOwnerPermission($owner_type, $error = true)
	{
		if(!($res = GW_Permissions::canAccess($owner_type, $this->app->user->group_ids)))
		{
			$this->setError(GW::l('/G/GENERAL/ACTION_RESTRICTED').' ("'.$owner_type.'"; "'.$res.'")');
		}
		
		return $res;
	}
	
	
	function canBeAccessed($item, $die = true) {
		$result = $this->checkOwnerPermission($item->owner_type);

		if (!$die || $result)
			return $result;

		
		$this->jump();
	}
	
	function getValidationParams($owner_type)
	{
		$names['storewh']='dimensions_resize';
		$names['minwh']='dimensions_min';
		$names['maxwh']='dimensions_min';
			
		$cfg = $this->app->sess("attachments/$owner_type");
		$cfgi = $cfg['image'];
		
		$i = [
			'dimensions_resize' => $cfgi['storewh'] ?? '5000x5000',
			'dimensions_min' => $cfgi['minwh'] ?? '1000x1000',
			'dimensions_max' => $cfgi['maxwh'] ?? '10000x10000',
		];
		
		$cfgf = $cfg['file'];
		
		$f = [
			'size_max' => isset($cfgf['size_max']) ? GW_File_Helper::parseSize($cfgf['size_max']) : GW_File_Helper::fileUploadMaxSize()
		];
		
		if(isset($cfgf['ext']))
			$f['allowed_extensions'] = $cfgf['ext'];
		
		
		
		return ['image'=>$i, 'file'=>$f, 'general'=>$cfg];
	}
	
	function getOwnerVals()
	{
		return [
		    'owner_type'=>$_GET['owner_type'], 
		    'owner_id'=>$_GET['owner_id'], 
		    'owner_temp_id'=>$_GET['owner_temp_id'],
		    'field'=>$_GET['field']
		];		
	}
	
	function doStore()
	{
		$insert=0;
		$errors=0;
		
		
		//print_r([$_FILES, $_POST]);
		//exit;
		
		$files = GW_File_Helper::reorderFilesArray('files');
		
		$owner_vals = $this->getOwnerVals();
		
		if(!$this->checkOwnerPermission($owner_vals['owner_type']))
			return false;
		
		
		
		$have_cnt = GW_Attachment::singleton()->count(GW_DB::buidConditions($owner_vals));
		
		$valid_params = $this->getValidationParams($owner_vals['owner_type']);
		
		
		
		foreach($files as $file)
		{
			if(isset($valid_params['general']['limit']) && $have_cnt >= $valid_params['general']['limit']){
				$this->setMessage([
				    'text'=>"/m/ATTACHMENTS_LIMIT_REACHED", 'type'=>GW_MSG_ERR, 'params'=>[
					'/G/general/LIMITATION'=>$valid_params['general']['limit'],
					'/G/general/ITEM'=>basename($file['name'])
				]]);
				
				unlink($file['tmp_name']);
				
				$errors++;
				continue;;
			}
				
			list($type,$subtype) = explode('/', $file['type']);
			
			
			$values = $owner_vals;
			
			$values['title'] = $file['name'];
			$values['priority'] = ++$have_cnt;
			
			
			$values['content_cat'] = $type == 'image' && in_array($subtype, ['png','jpeg','gif']) ? 'image':'file';
			
			$values['content_type'] = $file['type'];

			
			$item = GW_Attachment::singleton()->createNewObject($values);

			$item->setValidationParams($values['content_cat'], $valid_params[ $values['content_cat'] ]);
			
			$item->set($values['content_cat'], [
			    'new_file' => $file['tmp_name'], 
			    'size' => filesize($file['tmp_name']), 
			    'original_filename' => $file['name'] 
			]);
			
			
			
			//$this->setError(json_encode($item->composite_map));
			
			
			if($item->validate())
			{
				$item->insert();			
				$insert++;
			}else{
				$errors++;
				
				$this->setItemErrors($item);
			}
			

		}
		
		$out = $this->processView('listajax', ['return_as_string'=>true]);
		
		$this->setMessage("insert: $insert; errors: $errors");
		
		$this->app->addPacket(['action'=>'update_container','id'=>$_GET['dropid'], 'value'=>$out]);
		
	}
	
	function doSetPositions()
	{
		$owner_vals = $this->getOwnerVals();

		if(!$this->checkOwnerPermission($owner_vals['owner_type']))
			return false;		
		
		$positions = $_REQUEST['positions'];
		$positions = explode(',', $positions);
				
		if(!count($positions))
			return false;
		
		$rows = [];
		foreach($positions as $priority => $id)
			$rows[$id] = ['priority'=>$priority];
		
		$updated = GW_Attachment::singleton()->savePositionsExact($rows, GW_DB::buidConditions($owner_vals));
		
		$this->setMessage(["text"=>GW::l('/m/UPDATED_POSITIONS').": $updated", "type"=>GW_MSG_INFO]);
	}	
	
	
	
	function viewPreview()
	{
		$item = $this->getDataObjectById();
		$this->tpl_vars['item'] = $item;
	}
	
	function doPreview()
	{
		
		$this->processView('preview');
		exit;
		
	}
}
