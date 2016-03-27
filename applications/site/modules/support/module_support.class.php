<?php

class Module_Support  extends GW_Public_Module {

	function init() {		
		$this->config = new GW_Config('support/');
	}

	function viewDefault() {
		$this->tpl_name = 'support';
	}

	
	function encodeTextMessage($vals)
	{
		$str = '';
		foreach($vals as $key => $val)
		{
			$str .="{$key}\n------------\n{$val}\n";
		}
		return $str;
	}

	function doMessage() {
		$vals = $_POST['item'];
		$msg = GW_Support_Message::singleton()->createNewObject();
		
		$msg->setValues($vals);
		$msg->user_id = $this->app->user ? $this->app->user->id : 0;
		$msg->ip = $_SERVER['REMOTE_ADDR'];
	
		if ($msg->validate()) {
			$msg->insert();
			
			mail($this->config->notify_mail, 'New support request', $this->encodeTextMessage($vals));
			
			$this->app->jump(false,['success'=>1]);
		} else {
			$this->app->setErrors($msg->errors);
		}

		
	}


}
