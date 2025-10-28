<?php


class Module_Email_Queue extends GW_Common_Module
{	
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		$this->config = new GW_Config($this->module_path[0].'/');
		
		
		
		if(isset($_GET['to']))
			$this->filters['to']=$_GET['to'];
		
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['to'] = 1;	
		
		$this->initLogger();
				
	}
	
	function __eventAfterListParams(&$params)
	{		
		if(isset($_GET['searchbycontent'])){
			$search=$_GET['searchbycontent'];
			$params['conditions'] = GW_DB::mergeConditions ($params['conditions'], "body LIKE '%$search%'");
		}
	}
	
	function doSend($item=false, $functiononly=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		$itemcopy = $item;
		$status = GW_Mail_Helper::sendMail($itemcopy);
		
		if($status){
			if(!$functiononly)
				$this->setMessage("Mail id:{$item->id} SENT");
				
			$this->setMessage("Mail id:{$item->id} SENT");
			$item->status = "SENT";
		}else{
			if(!$functiononly)
				$this->setError("Mail id:{$item->id} FAILED ({$item->error})");
				
			$item->status = "ERR";
		}
		
		//pasikeicia i masyva po GW_Mail_Helper::sendMail vykdymo ir pats sendmail updatechaned padaro
		//$item->updateChanged();
		
		if($functiononly)
			return true;
		
		if($this->sys_call && !$this->isPacketRequest())
			$this->jump();
		
		
		$this->notifyRowUpdated($item->id, false);			
	}
	
	
	function getReady()
	{
		$limit = $this->config->mail_queue_portion_size ?: 5;
		
		return $this->model->findAll('status="ready"', ['limit'=>$limit]);		
	}
	
	function doSendQueue()
	{
		$limit = $this->config->mail_queue_portion_size ?: 5;
		$affected = GW_Mail_Queue::singleton()->updateMultiple(['scheduled < ? AND `status`="scheduled" ', date('Y-m-d H:i')], ['status'=>'ready'], $limit);
		
		if($affected)
			$this->setMessage("Scheduled switched to ready: $affected");
		
		$list = $this->getReady();
				
		foreach($list as $item){
			$ids[] = $item->id;
			$this->doSend($item, true);
			sleep(1);
		}
		
		$next = count($this->getReady());
		//ids processed: ".implode(',',$ids).".
		$this->setMessage("Next portion size: $next");
		
		/*
		if($this->getReady()){
			sleep(1);
			Navigator::backgroundRequest('admin/lt/emails/email_queue?act=doSendQueue');
		}
		 * 
		 */
	}
	
	
	function doViewBody($item=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		if($item->plain)
			header('Content-type: text/plain');

		die($item->body);
	}
	
	function dummy()
	{
		GW_Mail_Queue::singleton();
	}
	
	function __eventAfterForm()
	{
		$this->tpl_vars['form_width']="1000px";
		$this->tpl_vars['width_title']="120px";
		
	}
	
	function __eventAfterSave($item)
	{	
		if($_POST['submit_type']==7)
		{
			$this->doSend($item);
		}
	}
	

}
