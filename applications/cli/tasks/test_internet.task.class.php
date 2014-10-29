<?php

class Test_Internet_Task extends GW_Tasks_App
{	
	function process()
	{
		if(GW_Test_Internet::check($error)){
			$this->msg('Connected');			
		}else{
			
			$this->error_code=6;
			$this->error_message=$error;
			$this->msg('Disconnected');			
		}
	}
}