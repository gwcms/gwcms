<?php


class GW_Membership extends GW_Data_Object
{
	
	
	
	
	function isValid($time=false)
	{
		$time = $time ?: date('Y-m-d H:i:s');
		return $this->validfrom < $time && $time < $this->expires;
	}

	

}