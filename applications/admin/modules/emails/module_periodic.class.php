<?php


class Module_Periodic extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	

	function init()
	{
		parent::init();
		
	
	}

	
	function __eventAfterList($list)
	{
		$this->attachFieldOptions($list, 'template_id', 'GW_Mail_Template');
		
		$this->options['group_id'] = GW_NL_Group::singleton()->findAll(false,['key_field'=>'id']);
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
	
	function doRun($item = false)
	{
		if(! $item)
			$item = $this->getDataObjectById();
		
		
		
		
		$msg = new GW_NL_Message;
		$msg ->title = $item->title." (".GW::l('/m/PERIODIC').") #".$item->id. ' '.date('Y-m-d H');
		$template = $item->template;
		
		$fields = [
		    'subject_lt',
		    'subject_en',
		    'subject_ru',
		    'body_lt',
		    'body_ru',
		    'body_en',
		    ];
		GW_Array_Helper::objectCopy($template, $msg, $fields);
		$msg->groups = $item->groups;
		$msg->active = 1; 
		$msg->status = 10; 
		$msg->lang_lt = $template->ln_enabled_lt;
		$msg->lang_en = $template->ln_enabled_en;
		$msg->lang_ru = $template->ln_enabled_ru;
		$msg->updateRecipientsCount();
		
		$msg->insert();
		Navigator::backgroundRequest('admin/lt/emails/messages?act=doSendBackground');	
		
		
		
		
		$item->last_run = date('Y-m-d H:i:s');
		$item->updateChanged();
		
		$this->jump();
	}
	
	
	
	function doCheckAndRun()
	{
		
		$list = $this->model->findAll('active=1');
		
		
		foreach($list as $item){
			$time_match = $item->time_match;
			list($time_match, $interval) = explode('#', $time_match);


			if (strpos($time_match, ' ') === false)
				$time_match = '....-..-.. ' . $time_match;
			
			

			$match = preg_match("/$time_match/", date('Y-m-d H:i:s'), $m) ? 1 : 0;

			$last_exec = $item->last_run;



			$dif = time() - strtotime($last_exec);
			
			if(isset($_GET['debug'])){
				d::ldump(['id'=>$item->id,'time_match'=>$time_match, 'nowdate'=>date('Y-m-d H:i:s'), 'ismatching'=>$match, 'difference'=>$dif, 'interval'=>$interval]);
			}

			//debug
			//echo "lastexec $time_match#$interval - $last_exec\n";
			//echo "diff: $dif\n";
			//echo "exec?: ".($match && $dif >= $interval * 60 ?'yes':'no')."\n";

			if ($match && ($dif >= $interval * 60 )){
				$this->doRun($item);
			}
		
		}
	}	
}
