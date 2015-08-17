<?php

class GW_Message extends GW_Data_Object
{
	var $table='gw_messages';

	var $calculate_fields = ['list_color'=>'listColor'];
	
	var $default_order = "seen ASC, insert_time DESC";
	
	//use example:
	//GW_Adm_Message::msgStatic(1, "Ghost task", $msg, 'tasks_health');
	
	/**
	 * 
	 $group_messages=0 -dont group
	 $group_messages=2 - group
	 $group_messages=1 - group and merge messages
	 */
	function msg($to, $subj, $msg, $sender='', $group_messages=0, $escape=true)
	{
		if($escape){
			$msg = htmlspecialchars($msg);
			$msg = str_replace("\n", "<br />", $msg);
		}
		
		if($group_messages>0 && ($group = $this->find(['subject=? AND sender=? AND seen=0 AND user_id=?',$subj,$sender,$to])))
		{
			if($group_messages>1){
				$group->message.='<br /><br /><small style="color:silver">'.date('Y-m-d H:i:s').":</small><br />";
				$group->message.=$msg;
			}
			$group->group_cnt = $group->group_cnt+1;
			
			$group->updateChanged();
		}else{

			$msg = $this->createNewObject([
				'user_id'=>$to,
				'subject'=>$subj,
				'message'=>$msg,
				'sender'=>$sender,
			]);
			
			$msg->insert();
		}
	}
	
	function listColor()
	{		
		if(!$this->seen)
			return '#FFA347';
	}
	
}