<?

include __DIR__.'/module_items.class.php';

class Module_Comments extends Module_Items
{	
  	function init()
  	{
  		parent::init();
  		
 		$this->filters['parent_id']=GW::$request->path_arr[1]['data_object_id'];
 		$this->filters['type']=2;
  	}
  	
  	
  	function viewList()
  	{
  		if($id = $_REQUEST['id'])
  		{
  			$comment = $this->model->createNewObject($id);
  			$comment->load();
  			$this->smarty->assign('comment',$comment); 
  		}
  		  		
  		return parent::viewList();
  	}
  	
	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case "BEFORE_SAVE":
				$this->notifySave($context);
			break;
		}
	}
	
	function notifySave($comment)
	{
		//dump($item);
		

  		$parent = $this->model->createNewObject($comment->parent_id);
  		$parent->load();
  		
  		$usr_create = GW_Adm_User::getById($parent->user_create);
  		$usr_exec = GW_Adm_User::getById($parent->user_exec);
  		$user_comment = GW::$user;
  		
  		$emails = Array();
  		
  		if($user_comment->id != $usr_create->id && $usr_create->email)
  			$emails[] = $usr_create->email;
  			
  		if($usr_exec && $user_comment->id != $usr_exec->id && $usr_exec->email)
  			$emails[] = $usr_exec->email;
				
		$msg = Array();
		$msg['from']=$user_comment->email;
		$msg['subject']=GW::$static_conf['PROJECT_NAME']." :: New comment on task '".$parent->title."'";
		$msg['body']="Link: \n".Navigator::getBase(true).GW::$request->ln."/todo/items/".$parent->id."/form\n\nComment:\n".
		$comment->description;
		
		
		
		foreach($emails as $email)
		{
			$sent = GW_Mail::simple(Array('to'=>$email) + $msg);
			
			if(!$sent){
				$this->setErrors("Notification failed");
			}else{
				GW::$request->setMessage("Notifcation sent($sent) to $email");
			}
		}	
	}
}