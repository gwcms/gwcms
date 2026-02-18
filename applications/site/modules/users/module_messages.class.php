<?php

//suformuot service workeriui json
/*
 * pvz: chat zinutes
{
    "title": "EverPresent",
    "body": "whats up dude ? ",
    "tag": "chat-notification-tag",
    "url": "/",
    "icon": "/tools/img/390549c310984b0e4809a4f1f8fb8a53?size=115x115&method=crop&v=0"
}
 */

class Module_Messages extends GW_Public_Module {

	function init() {
		$this->model = new GW_Customer;

		//tol kol dirbu su siuo moduliu - reikia kad lang failus importuotu i db
		GW::$devel_debug = true;

	}

	function viewJson()
	{
		$data=[];
		
		if(!$this->app->user)
			die('not authorized');
		
		
		if(isset($this->app->user_updates['new_messages'])){
			
			$lastmessage=GW_Chat_Message::singleton()->find(['to_id=?',$this->app->user->id],['order'=>'insert_time DESC']);
			
			
			if($lastmessage){
				$sender = GW_Customer::singleton()->find(['id=?', $lastmessage->from_id]);
				
				$data['title'] = "New message from ".$sender->name;
				$data['body'] = $lastmessage->message;
				$data['tag'] = 'chat-notification-tag';
				$data["url"] =  $this->app->buildUri(GW::s('SITE/PATH_TRANS/users/chat/_'));
				
				if($im=$sender->profilefoto)
				{
					
					$data["icon"] = '/tools/img/'.$im->key.'?size=115x115&method=crop&v='.$im->v;
				}else{
					$data["icon"] = $this->app->app_root.'assets/img/logo_push_messages.png';
				}
				
			}
			
			
			
		}
		
		
		//chat messages
		
		
		
		//system messages
		/*
		if(isset($lastmessage))
		{
			
			
			
			$item = $messages[0];
			$data["body"]=$item->message;
			$data['title']=$item->subject;
			$data["icon"] = $this->app->app_root.'static/img/logo_push_messages.png';
			$data['tag'] = 'simple-push-demo-notification-tag';
			$data["url"] =  Navigator::getBase();
			
		}*/
		
		
		
		header('Content-type: application/json');

		if($data){
			echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		}else{
			die('No updates');
		}
	
		
		exit;
	}
	
	function viewUpdates(){
		
		header('Content-type: application/json');
		die(json_encode($this->app->user_updates, JSON_PRETTY_PRINT));
	}	
	

}
