<?php

class GW_Message extends GW_Data_Object {

	var $table = 'gw_messages';
	var $calculate_fields = ['list_color' => 'listColor'];
	var $default_order = "seen ASC, insert_time DESC";

	//use example:
	//GW_Adm_Message::msgStatic(1, "Ghost task", $msg, 'tasks_health');

	/**
	 * 
	  $group_messages=0 -dont group
	  $group_messages=2 - group
	  $group_messages=1 - group and merge messages
	 */
	function msg($to, $subj, $msg, $sender = '', $group_messages = 0, $escape = true, $params=[]) {
		
		$level = isset($params['level']) ? $params['level'] : 0;
		
		
		
		if ($escape) {
			$msg = htmlspecialchars($msg);
			$msg = str_replace("\n", "<br />", $msg);
		}

		if ($group_messages > 0 && ($group = $this->find(['subject=? AND sender=? AND seen=0 AND user_id=?', $subj, $sender, $to]))) {
			if ($group_messages > 1) {
				$group->message.='<br /><br /><small style="color:silver">' . date('Y-m-d H:i:s') . ":</small><br />";
				$group->message.=$msg;
			}
			$group->group_cnt = $group->group_cnt + 1;

			$group->updateChanged();
		} else {

			$msg = $this->createNewObject([
				'user_id' => $to,
				'subject' => $subj,
				'message' => $msg,
				'sender' => $sender,
				'level' => $level
			]);

			$msg->insert();
		}
	}
	
	/** 
		same as msg jus all params in array
		[
			'to'=>,
			'subject'=>,
			'message'=>,
			'sender'=>,//optional
			'group'=>,//optional
			'escape'=>//optional
		]
	 */
	function message($data)
	{
		$default = [
			'to'=>'',
			'subject'=>'',
			'message'=>'',
			'sender'=>1,//system user
			'group'=>1,
			'escape'=>true
		];	
		
		$data = array_merge($default, $data);
		
		return self::msg($data['to'], $data['subject'], $data['message'], $data['sender'], $data['group'], $data['escape'], $data);
		
	}

	function listColor() {
		if (!$this->seen)
			return '#FFA347';
	}
	
	public $push_log;
	
	function eventHandler($event, &$context_data = []) {
		switch ($event) {
			case 'AFTER_INSERT':
				if($this->level >= 10 && $this->level <= 20){
					if($this->level >= 15)
						$this->push_log = GW_Android_Push_Notif::push($this->user_id);
					else
						$this->push_log = GW_Android_Push_Notif::pushIfNotOnline($this->user_id);
				}
		}
		
		parent::eventHandler($event, $context_data);
	}
	

}
