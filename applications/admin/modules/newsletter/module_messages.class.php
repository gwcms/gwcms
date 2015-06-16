<?php


class Module_Messages extends GW_Common_Module
{	

	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;
		$this->options['groups']=GW::getInstance('GW_NL_Groups')->getOptions();
		
	}

	function viewForm()
	{
		parent::viewForm();
		
		$this->options['groups']=GW::getInstance('GW_NL_Groups')->getOptionsWithCounts();		
	}
	
	function viewDefault()
	{
		$this->viewList();
		
		$this->smarty->assign('lasttestmail', GW::getInstance('GW_Config')->get('newsletter/lastmail'));
	}

	//overrride me || extend me
	function eventHandler($event, &$context)
	{
		switch($event)
		{
			case 'BEFORE_SAVE':
				$item=$context;
				
				//$item->beforeSaveParseRecipients();
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
		

		
		//$item->saveValues(['sent_info'=>  json_encode($recip), 'sent_count'=>$sent_count]);
		
		$this->jump();
	}
	
	function __doSend($item, $recipients)
	{
		foreach($recipients as $recipient){
			
			$msg = $item->body;
			$subj = $item->subject;
			
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
				$this->app->setMessage("$recipient->name &gt; $recipient->email :: OK");
				$recipient->sent=true;
				$sent_count++;
			}
		}
		
		return ['sent'=>$sent_count];
	}
	
	
	function doTest()
	{
		if(! $item = $this->getDataObjectById())
			return false;		
		
		$mail = $_REQUEST['mail'];
		
		GW::getInstance('GW_Config')->set('newsletter/lastmail', $mail);
		
		$this->__doSend($item, [(object)['id'=>-1,'name'=>'Testname', 'surname'=>'Testsurname', 'email'=>$mail]]);
		
		
		$this->app->setMessage('Testinis laiškas išsiųstas į: '.$mail);
		$this->jump();
	}	
}
