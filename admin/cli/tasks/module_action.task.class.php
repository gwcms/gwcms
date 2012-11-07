<?php

class Module_Action_Task extends GW_Tasks_App
{
	function process()
	{
		echo GW_Request::innerProcessStatic($this->data->arguments['request']);
	}
}