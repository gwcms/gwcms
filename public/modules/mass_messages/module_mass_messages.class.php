<?php


class Module_Mass_Messages extends GW_Public_Module
{

	function init()
	{
		$this->model = new GW_Mass_Message;
	}

	
	function viewForm()
	{
					
		
			
		$item = $this->model->createNewObject();
		
		if(isset($_SESSION['error_item'])){
			$item = $this->model->createNewObject();
			$item->setValues($_SESSION['error_item']);
			unset($_SESSION['error_item']);
			
			
			$this->smarty->assign('item_errors', $_SESSION['item_errors']);	
			
			unset($_SESSION['item_errors']);
		}
		
		//d::dumpas($item);
		
		if(!$item->sender)
			$item->sender = GW::$user->last_sms_sender;
			
		$this->smarty->assign('item', $item);
	}

	function viewDefault($params)
	{
		$this->processView('form');
	}
	
	function doSendSms()
	{
		$vals = $_POST['sms'];
		
		$item = $this->model->createNewObject();
		

		
		$item->setValues([
		    'user_id'=>GW::$user->id,
		    'sender'=>$vals['sender'],
		    'message'=>$vals['message'],
		    'recipients'=>$vals['recipients'],
		    'status'=> $_POST['send'] ? 1 :0,
		    'send_time'=> isset($vals['defer']) ? $vals['send_time'] : 0
		]);
		
		//d::dumpas($item->toArray());
		
		if(!$item->validate())
		{			
			$this->setErrors($item->errors);
					
			$_SESSION['error_item']=$item->toArray();				
			$_SESSION['item_errors']=$item->errors;	
			
			GW::$request->setErrors($this->lang['sms_validation_failed']);
			GW::$request->jump();
		}
		
		$item->save();
		
		//GW::getInstance('GW_Config')->set('new_sms/sms_sender', $item->sender);
		GW::$user->saveValues(['last_sms_sender'=>$item->sender]);

		
		GW::$request->setMessage($item->status==1 ? $this->lang['sms_placed_for_sending'] : $this->lang['sms_saved']);
		GW::$request->jump();
	}
	
	
	function viewList()
	{
		$list = $this->model->findAll(Array('user_id=?',GW::$user->id));
		
		$this->smarty->assign('list', $list);
	}

	

	
}
