<?php


class Module_Module_Fields extends GW_Common_Module
{
	public $filterpaths=false;
	
	function init()
	{
		$this->model = GW_Adm_Page_Fields::singleton();
		parent::init();
		
		$this->modid = $this->app->path_arr['1']['data_object_id'] ?? false;
		
		
		if($this->modid){
			$page = GW_ADM_Page::singleton()->find(['id=?', $this->modid]);

			if(isset($page->info->model))
				$this->filters['parent'] = strtolower($page->info->model);		
		}
		
		if(isset($_GET['path'])){
			$page = GW_ADM_Page::singleton()->find(['path=?', $_GET['path']]);

			if(isset($page->info->model))
				$this->filters['parent'] = strtolower($page->info->model);		
		}		
		
		$this->app->carry_params['path']=1;
	}
	
	
	function __eventAfterListParams(&$params)
	{		
		if($this->filterpaths)
			$params['conditions'] = GW_DB::mergeConditions ($params['conditions'], GW_DB::inConditionStr ("path", $this->filterpaths));
	}	
	
	
	function __eventBeforeSave0($item)
	{
		
		
		if($item->changed_fields || !$item->id){
			
			$this->modifDbStructure($item);
		}
	/*
		unset($item->content_base['order_enabled']);
		unset($item->content_base['condition_enabled']);
		unset($item->content_base['fields_enabled']);
		unset($item->content_base['pageby_enabled']);
	*/	
	}
	
	
	function __eventBeforeDelete($item)
	{
	
				
		if($this->allowChange($this->getFieldsInfo($item), $item->fieldname))
		{
			$sql = "ALTER TABLE  `$item->parent` DROP  `$item->fieldname`";
			GW::db()->query($sql, true);
			$this->setMessage($sql);
		}
			
	}
	
	function getFieldsInfo($item)
	{
		return GW::db()->fetch_rows_key("SHOW FULL  COLUMNS FROM `$item->parent`", 'Field');
	}
	
	function allowChange($fieldsInfo, $fieldname, $errorShow = true)
	{
		$res = ($fieldsInfo[$fieldname]['Comment'] ?? false) == 'gwcms';
		
		if($errorShow && !$res){
			$this->setError("Not allowed to change field ");
		}
		return $res;
	}
	
	function modifDbStructure($item)
	{
		$fieldsInfo = $this->getFieldsInfo($item);

		
		if($item->type=="extended")
			return false;
		
		$fieldname = $item->fieldname;
		
		switch($item->inp_type){
			
			case 'date':
				$type = "timestamp";
			break;
			case 'bool':
				$type = "tinyint";
			break;
			case 'radio':
			case 'select_plain':
			case 'select_ajax':
				$type = "INT";
			break;	
			case 'number':
				$type = "FLOAT";
			break;
			case 'password':
			case 'tags':
			case 'url':
			case 'email':
			case 'text':
				$type = "VARCHAR(255)";
			break;
			case 'color':
				$type = "VARCHAR(7)";
			break;
			case 'code':
			case 'code_json':
			case 'code_smarty':
			case 'textarea':
				$type = "TEXT";
			break;	
			case 'file':
			case 'image':
			case 'attachments':
				$type = null;
			break;
			default:
				$type = "VARCHAR(255)";
		}
		
		if(!$type)
			return false;
		
		$null = "NULL";
		$default="";
		$comment = "COMMENT  'gwcms'";	//tik pazymeti bus leidziami redaguoti, salinti	
		
		$after = "";
		

		
		
		if(!isset($fieldsInfo[$item->fieldname])){
			$method="ADD";
			$after = array_key_last($fieldsInfo);
			$after="AFTER  `$after`";
		}else{
			if(!$this->allowChange($fieldsInfo, $fieldname)){
				return false;
			}			
			
			$method="CHANGE `$fieldname`";
		}
	
		$sql = "ALTER TABLE  `$item->parent` $method  `$fieldname` $type $null $default $comment  $after ;";		

		GW::db()->query($sql, true);
		//d::dumpas($sql);
		
		$this->setMessage($sql);
		return true;
	}
	
	function getInputTypes(){
		$list = [];
		foreach(glob(GW::s('DIR/ADMIN/TEMPLATES').'elements/inputs/*.tpl') as $fname)
		{
			$tmp = pathinfo($fname, PATHINFO_FILENAME);
			$list[$tmp] = $tmp;
		}
		
		return $list;
	}

	

}
