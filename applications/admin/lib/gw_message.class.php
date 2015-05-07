<?php

class GW_Message extends GW_Data_Object
{
	var $table='gw_messages';

	var $calculate_fields = Array('list_color'=>'listColor');
	
	var $default_order = "seen ASC, insert_time DESC";
	
	//use example:
	//GW_Adm_Message::msgStatic(1, "Ghost task", $msg, 'tasks_health');
	
	function msg($to, $subj, $msg, $sender='')
	{
		
		if($group = $this->find(Array('subject=? AND sender=? AND seen=0 AND user_id=?',$subj,$sender,$to)))
		{
			$group->message.="\n\n---------------------------\n";
			$group->message.=date('Y-m-d H:i:s').":\n---------------------------\n";
			$group->message.=$msg;
			$group->group_cnt = $group->group_cnt-1+2;
			
			$group->update(Array('message','group_cnt'));
		}else{

			$msg = $this->createNewObject(Array(
				'user_id'=>$to,
				'subject'=>$subj,
				'message'=>$msg,
				'sender'=>$sender,
			));
			
			$msg->insert();
		}
	}
	
	function listColor()
	{		
		if(!$this->seen)
			return '#FFA347';
	}
	
}