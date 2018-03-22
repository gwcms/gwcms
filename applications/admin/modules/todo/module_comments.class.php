<?php

include __DIR__.'/module_items.class.php';

class Module_Comments extends Module_Items
{	
  	function init()
  	{
		$this->model = GW_Todo_Item::singleton();
		
  		parent::init();
  		
		
 		$this->filters['parent_id']=$this->app->path_arr[1]['data_object_id'];
 		$this->filters['type']=2;
		
		//d::dumpas($this->filters);
		
		$this->model->default_order = 'id ASC';
  	}
  	
  	
  	function __eventAfterList()
  	{
		if(isset($_REQUEST['id']))
  		{
  			$comment = $this->model->createNewObject($_REQUEST['id']);
  			$comment->load();
  			$this->tpl_vars['comment'] =& $comment; 
  		}	
  	}

	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case "BEFORE_SAVE":
				$item = $context;
				
				$item->user_create = $this->app->user->id;
				
				$item->description = str_replace("\n", "<br />", $item->description);
				
				$this->notifySave($context);
			break;
			case 'AFTER_FORM':
				$item = $context;
				$item->description = str_replace('<br />',"\n", $item->description);
			break;
		}
		
		parent::eventHandler($event, $context);
	}
	
	function notifySave($comment)
	{
  		$parent = $this->model->createNewObject($comment->parent_id);
  		$parent->load();
  		
  		$usr_create = GW_User::getById($parent->user_create);
  		$usr_exec = GW_User::getById($parent->user_exec);
  		$user_comment = $this->app->user;
  		
  		$emails = Array();
  		
		//jei pakomentaves vartotojas nera tasko autorius, prideti emaila
  		if($user_comment->id != $usr_create->id && $usr_create->email)
  			$emails[] = $usr_create->email;
  			
		//jei pakomentaves artotojas nera tasko vykdytojas, prideti emaila
  		if($usr_exec && $user_comment->id != $usr_exec->id && $usr_exec->email)
  			$emails[] = $usr_exec->email;
				
		
		$tmp = str_replace('<br />',"\n", $comment->description);
		
		$msg = Array();
		$msg['from']=$user_comment->email;
		$msg['subject'] = GW::s('PROJECT_NAME')." :: New comment on task '".$parent->title."'";
		$msg['body']="Link: \n".Navigator::getBase(true).$this->app->ln."/todo/items/".$parent->id."/form\n\nComment:\n".
		$tmp;
		
		
		
		foreach($emails as $email)
		{
			$sent = GW_Mail::simple(Array('to'=>$email) + $msg);
			
			if(!$sent){
				$this->setError("Notification failed");
			}else{
				$this->setPlainMessage("Notifcation sent($sent) to $email");
			}
		}	
	}
	
	//workaround kad nepaimtu is items
	function loadViews($page=false)
	{
		
	}
	
	function loadOrders($page=false)
	{
		
	}	
	
	function __eventBeforeListParams(&$params)
	{
		//nes module_items turi
	}
	
	function getListConfig()
	{
		return ['fields'=>[]];
	}
	
}