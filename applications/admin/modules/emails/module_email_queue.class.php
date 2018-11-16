<?php


class Module_Email_Queue extends GW_Common_Module
{	
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
	}
	
	function doSend($item=false)
	{
		if(!$item)
			$item = $this->getDataObjectById();
		
		$status = GW_Mail_Helper::sendMail($item);
		
		
		if($status){
			
			$this->setMessage("Mail id:{$item->id} SENT");
		}else{
			$this->setError("Mail id:{$item->id} FAILED ({$item->error})");
		}
		
		
		if($this->sys_call && !$this->isPacketRequest())
			$this->jump();
		
		
		$this->notifyRowUpdated($item->id, false);			
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

	
}
