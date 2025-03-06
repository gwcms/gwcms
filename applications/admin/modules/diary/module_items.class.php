<?php


class Module_Items extends GW_Common_Module_Tree_Data
{
	
	public $list_params=['page_by'=>100];	
	
	function init()
	{	
		parent::init();
		$this->filters['active']=1;
		$this->list_params['paging_enabled']=1;
		
		
		
	
		$this->initModCfg();
		
		$this->modconfig->last_request = date('Y-m-d H:i:s');
		
	}

	
	function getMoveCondition($item)
	{
		$tmp = $this->filters;
		$tmp['type']=$item->get('type');
		
		return GW_SQL_Helper::condition_str($tmp);
	}
	
	
	function doDelete()
	{
		$do=$this->getDataObjectById();
		$do->set('active', 0);
		$do->update();
		
		$this->jump();
	}
    
	
	function doAjaxSave()
	{
		
		$vals = $_REQUEST['item'];	
		
		$item = $this->model->createNewObject($vals);
		
		if($item->id)
			$item->load();
			
		$item->setValues($vals);
		
		$item->save();
		
		
		exit;
	}	
	
	
	
	function doAutoClear()
	{
		
	}

	function __eventAfterSave($item)
	{
		
		$this->doMigrate();
		
	}
	
	function doAutoLock()
	{
		//
		
	}
	
	function __getSecret()
	{
		$secret = file_get_contents($url=base64_decode($this->modconfig->safestorage_url));
		
		if(!$secret || strpos($secret,'404 not found')!==false){
			die('Crypt service not available');
		}
		return $secret;
	}
	
	function doMigrate()
	{
		
		$crpytkey = $this->__getSecret();
		
		$q = GW_DB::prepare_query(["UPDATE diary_entries SET text_crpt = AES_ENCRYPT(text, ?) WHERE LENGTH(text_crpt) = 0", $crpytkey]);
		GW::db()->query($q);
		
	}

}