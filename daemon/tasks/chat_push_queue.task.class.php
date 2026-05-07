<?php

class Chat_Push_Queue_Task extends GW_Tasks_App
{
	var $max_execution_time = 120;
	var $single_instance = 1;

	function process()
	{
		$limit = (int)($this->data->arguments['limit'] ?? 50);
		$result = GW_Chat_Service::singleton()->processPrivatePushQueue($limit);
		$this->msg($result);
	}
}
