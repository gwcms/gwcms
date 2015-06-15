<?php


class Module_Messages extends GW_Common_Module
{	

	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;		
		
	}

	
	function viewDefault()
	{
		$this->viewList();
	}

	//overrride me || extend me
	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case 'BEFORE_SAVE':
				$item=$context;
				
				$item->beforeSaveParseRecipients();
			break;
		}
		
		//pass deeper
		//parent::eventHandler($event, $context);
	}
	
	function doSend()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		$recip = json_decode($item->recipients_data);
		
		$sent_count = $item->sent_count;
		
		foreach($recip as $recipient){
			
			$msg = $item->{"body_".$recipient->lang};
			$subj = $item->{"subject_".$recipient->lang};
			
			$msg = str_replace('%NAME%', $recipient->name, $msg);
			
			//d::dumpas([$recipient, $msg, $subj, $item->sender]);
			
			
			//d::dumpas([$msg, $data]);
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			if($item->sender)
				$headers .= "From: $item->sender\r\n";



			if(!mail($recipient->email, $subj, $msg, $headers))
			{
				$this->app->setErrors("Įvyko klaida. Nepavyksta išsiųsti į $recipient->email");
				$recipient->sent = false;
			}else{
				$this->app->setMessage("$recipient->name &gt; $recipient->email :: $recipient->lang :: OK");
				$recipient->sent=true;
				$sent_count++;
			}
		}
		
		$item->saveValues(['sent_info'=>  json_encode($recip), 'sent_count'=>$sent_count]);
		
		$this->jump();
	}
}
