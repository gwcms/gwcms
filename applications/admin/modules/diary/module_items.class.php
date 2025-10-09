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
		
		
		$this->initLogger();
		
		if(!isset($_GET['cron'])){
			
			//d::dumpas('last_request update');
			$this->modconfig->last_request = date('Y-m-d H:i:s');
		}
		
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
    
	
	/*
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
	*/
	
	

	function __eventAfterSave($item)
	{	
		$crpytkey = $this->__getSecret();
		$q = GW_DB::prepare_query(["UPDATE diary_entries SET text_crpt = AES_ENCRYPT(text, ?) WHERE id=?", $crpytkey, $item->id]);
		GW::db()->query($q);
		if($cnt=GW::db()->affected())
			$this->setMessage("Crypt stored cnt: $cnt");
		
		$this->lgr->msg('Encrpt id:'.$item->id.' uid:'.$this->app->user->id.' ip: '.$_SERVER['REMOTE_ADDR']);
	}
	
	function doLock($jump=true)
	{
		GW::db()->query("UPDATE diary_entries SET text='hidden' WHERE text!='hidden'");
		$this->modconfig->unlocked = 0;
		
		if($jump){
			$this->jump();
		}
	}
	
	function doAutoLock()
	{
		$secs_since_last_request =time() - strtotime($this->modconfig->last_request);
		if($this->modconfig->unlocked && ($secs_since_last_request > 500)){
			$this->lgr->msg('Locking uid:'.$this->app->user->id.' ip: '.$_SERVER['REMOTE_ADDR']);
		
			$this->doLock(false);			
		}else{
			//$this->lgr->msg([$this->modconfig->unlocked, 'secs_since_last_req'=>$secs_since_last_request]);
		}
			
		die('test');
	}
	
	function doUnlock($nojump=false)
	{
		$sel=[];
		$form = ['fields'=>['pin'=>['type'=>'password', 'required'=>1]],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/PROVIDE_PIN_PLEASE'), ['method'=>'post'])))
			return false;	
		
		if($answers['pin']!=base64_decode($this->modconfig->decode_pin)){
			$this->setError("Pin error");
			return $this->jump();
		}
		
		
		$crpytkey = $this->__getSecret();
		
		$q = GW_DB::prepare_query(["UPDATE diary_entries SET text = AES_DECRYPT(text_crpt, ?) WHERE text='hidden'", $crpytkey]);
		GW::db()->query($q);
		if($cnt=GW::db()->affected())
			$this->setMessage("Decrypt cnt: $cnt");
		
		$this->lgr->msg('Unlocking uid:'.$this->app->user->id.' ip: '.$_SERVER['REMOTE_ADDR']);
		
		$this->modconfig->unlocked = 1;
		
		
		if(!$nojump){
			d::dumpas($nojump);
			sleep(1);
			$this->jump();
		}
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
		if($cnt=GW::db()->affected())
			$this->setMessage("Crypt stored cnt: $cnt");		
	}

	
	function __eventAfterList()
	{		
		if(!$this->modconfig->unlocked && ($_GET['act']??false)!='doUnlock')
		{
			$this->doUnlock(true);
		}
	}
	
	
}