<?php


class Module_Email_Queue extends GW_Common_Module
{	
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
	}
	
	function doSend($item=false, $functiononly=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		$status = GW_Mail_Helper::sendMail($item);
		
		if($status){
			if(!$functiononly)
				$this->setMessage("Mail id:{$item->id} SENT");
				
			$this->setMessage("Mail id:{$item->id} SENT");
			$item->status = "SENT";
		}else{
			if(!$functiononly)
				$this->setError("Mail id:{$item->id} FAILED ({$item->error})");
				
			$item->status = $item->error;
		}
		
		$item->updateChanged();
		
		if($functiononly)
			return true;
		
		if($this->sys_call && !$this->isPacketRequest())
			$this->jump();
		
		
		$this->notifyRowUpdated($item->id, false);			
	}
	
	
	function getReady()
	{
		return $this->model->findAll('status="ready"', ['limit'=>50]);		
	}
	
	function doSendQueue()
	{
		$list = $this->getReady();
		
		foreach($list as $item){
			$this->doSend($item, true);
		}
		
		if($this->getReady()){
			sleep(1);
			Navigator::backgroundRequest('admin/lt/emails/email_queue?act=doSendQueue');
		}
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
