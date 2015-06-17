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
				
				$item->recipients_count = count($item->getRecipients());
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
		
		
		
		$recipients = $item->getRecipients();
		
		$item->saveValues(['status'=>10]);
		
		
		$info = $this->__doSend($item, $recipients);
		$item->setValues($info);
			
		
		$item->sent_time = date('Y-m-d H:i:s');
		$item->status = 70;
		
		
		$this->app->setMessage($this->lang['SENT'].count($recipients).'/'.$item->sent_count);
		
		$item->update(['status','sent_count', 'sent_info', 'sent_time']);
		
		//$item->saveValues(['sent_info'=>  json_encode($recip), 'sent_count'=>$sent_count]);
		
		$this->jump($this->app->path.'/sentinfo');
	}
	
	function viewSentInfo()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		$this->smarty->assign('item', $item);
	}
	
	
	
	function __doSend($item, $recipients)
	{
		$sent_info = [];
		$sent_count=0;
		
		
		$msg = $item->body;
		
		$unsubscribe_link = Navigator::getBase(true).'site/';	
		
		foreach($recipients as $recipient){
			
			$msg = $item->body;
			$subj = $item->subject;
			
			$msg = str_replace('%NAME%', $recipient->name.' '.$recipient->surname, $msg);
			
			$us_link=$unsubscribe_link.strtolower($recipient->lang)."/direct/newsletter/newsletter/subscribe?nlid={$item->id}&rid=";	
			$us_link=$us_link.base64_encode($recipient->email);
			$us_link='<a href="'.$us_link.'">'.$this->lang['UNSUBSCRIBE_LINK'][strtoupper($recipient->lang)].'</a>';
			$msg = str_replace('%UNSUBSCRIBE%', $us_link, $msg);
			
			//d::dumpas([$recipient, $msg, $subj, $item->sender]);
			
			
			//d::dumpas([$msg, $data]);
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			if($item->sender)
				$headers .= "From: $item->sender\r\n";

			$info = new stdClass();
			GW_Array_Helper::objectCopy($recipient, $info, ['id','name','surname','email']);
			
			$info->time = date('Y-m-d H:i:s');
			
			if(!mail($recipient->email, $subj, $msg, $headers)) {
				$info->sent = false;
			}else{
				$info->sent = true;
				$sent_count++;
			}
			
			$sent_info[]=$info;
		}
		
		return ['sent_count'=>$sent_count, 'sent_info'=>$sent_info];
	}
	
	
	function doTest()
	{
		if(! $item = $this->getDataObjectById())
			return false;		
		
		$mail = $_REQUEST['mail'];
		
		GW::getInstance('GW_Config')->set('newsletter/lastmail', $mail);
		
		$info = $this->__doSend($item, [(object)['id'=>-1,'name'=>'Testname', 'surname'=>'Testsurname', 'email'=>$mail,'lang'=>'lt']]);
		
		if($info['sent_count']) {
			$this->app->setMessage('Testinis laiškas išsiųstas į: '.$mail);
		} else {
			$this->app->setMessage('Testinis laiško siuntimas nepavyko. Gavėjas: '.$mail);
		}
		$this->jump();
	}	
}
