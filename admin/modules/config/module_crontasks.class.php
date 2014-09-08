<?php


class Module_CronTasks extends GW_Common_Module
{	

	function init()
	{
		parent::init();
	}

	
	function viewDefault()
	{

		
		$this->viewList();

		
		//dump($this->smarty->tpl_vars['list']);
		
	}

	function zero($txt,$fillto=2,$fillchar='0'){
		return sprintf("%'{$fillchar}{$fillto}s",$txt);
	}	
	
	function dotestTimeMatch()
	{
		if(! $item = $this->getDataObjectById())
			return false;

		$time_match = $item->time_match;
		
		if(strpos($time_match,' ')!==false)
			list($date_match,$time_match) = explode(' ',$time_match);
		
		list($time_match, $interval) = explode('#', $time_match);
		
		d::ldump("Simulating time from 00:00:00 to 23:59:59");
		d::ldump("TimeMatch: ".$time_match.' Interval: '.$interval);
		
		$lastrun=-100000000;
		$runcnt=0;
		
		for($h=0;$h<24;$h++)
			for($m=0;$m<60;$m++)
				for($s=0;$s<60;$s++)
				{
					$time=self::zero($h).':'.self::zero($m).':'.self::zero($s);
					
					if(preg_match("/$time_match/",$time,$x))
					{
						$secs = $h*3600+$m*60+$s;
						
						if($secs - $lastrun > $interval*60)
						{
							d::ldump("$time Run! ");
							$lastrun=$secs;
							$runcnt++;
						}

					}
				
				}
				
		d::ldump("Run count: $runcnt");
		
	}
	
	function viewInfo()
	{
	}
	
	function doRun()
	{
		if(! $item = $this->getDataObjectById())
			return false;

		GW_Tasks_App::runDirect($item->name, Array('debug'=>1)+(array)json_decode($item->params, true));
	}
}
