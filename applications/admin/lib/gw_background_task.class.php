<?php

class GW_Background_Task
{
	use Singleton;
	
	public function __construct($vals=[]) {
		
		foreach($vals as $key => $val)
			$this->$key = $val;
		
		if(!isset($this->id))
			$this->id = time().GW_String_Helper::getRandString(10);
		
		if(!isset($this->start))
			$this->start = time();
		
		if(!is_numeric($this->expire))
			$this->expire = strtotime($this->expire);
		
	}
	
	function executionTime()
	{
		return GW_Math_Helper::uptime(time()- $this->start);
	}
	
	
	function isExpired()
	{
		return time() > $this->expire;
	}
	
	function checkExpired(&$store=-1)
	{
		if($store===-1)
			$store =& GW::$context->vars['app']->sess['bgtasks'];
		
		foreach($store as $id => $bgtask)
		{			
			if($bgtask->isExpired()){
				unset($store[$id]);
			}
		}
	}
}
