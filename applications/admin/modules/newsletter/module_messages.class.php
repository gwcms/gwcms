<?php


class Module_Messages extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
	}

	function __eventAfterForm()
	{
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptionsWithCounts();		
	}
	
	
	function __addHitCounts(&$list)
	{
		#attach counts
		$counts = GW::getInstance('GW_NL_Hit')->countGrouped('message_id', GW_DB::inCondition('message_id', array_keys($list) ));		
		
		foreach($list as $id => $item)
			$item->hit_count = isset($counts[$id]) ? $counts[$id] : 0;
	}	
	
	function __eventAfterList(&$list)
	{
		$this->tpl_vars['lasttestmail']=GW::getInstance('GW_Config')->get('newsletter/lastmail');
		
		$this->__addHitCounts($list);
	}
	
	function __eventBeforeSave($item)
	{
		$item->recipients_count = $item->getRecipientsCount();
		
		$item->body = preg_replace('/<p>[^\da-z]{0,20}&nbsp;[^\da-z]{0,20}<\/p>/iUs', '', $item->body);		
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
		
		return ['item'=>$item];
	}
	
	
	function __prepareMessage(&$msg, $letter, $linkbase , $recipient)
	{		
		# 1 - VARDAS PAVARDE
		$this->smarty->assign('NAME', $recipient->name.' '.$recipient->surname);

		$linkbase = $linkbase.strtolower($recipient->lang)."/direct/newsletter/newsletter/";
		
		
		# 2 - ATSISAKYMO LINKAS
		
		$reml_encoded = base64_encode($recipient->email);
		$us_link=$linkbase."subscribe?nlid={$letter->id}&re=";	
		$us_link=$us_link.$reml_encoded;
		$us_link='<a href="'.$us_link.'">'.$this->lang['LINK'][strtoupper($recipient->lang)].'</a>';
		
		$this->smarty->assign('UNSUBSCRIBE', $us_link);
		
		$link_online = $linkbase.'item?nlid='.$letter->id.'&rid='.$recipient->id.'&re='.$reml_encoded;
		$link_online = '<a href="'.$link_online.'">'.$this->lang['LINK'][strtoupper($recipient->lang)].'</a>';
		$this->smarty->assign('LINKONLINE', $link_online);
		
		# 3 - TRACKING PRIDEJIMAS
		$trackinglink=$linkbase."link?nlid={$letter->id}&rid=".$recipient->id.'&link=';
			
		$msg = $this->smarty->fetch('string:'.$msg);		
		
		$msg = str_replace('##TRACKINGLINK##', $trackinglink, $msg);
	}
	
	
	function __doSend($item, $recipients)
	{
		$sent_info = [];
		$sent_count=0;
		
		$linkbase = Navigator::getBase(true).'site/';	
		
		$message = $item->body_full;
		
		//$message="<a href='http://www.menuturas.lt/newsletter_images/nl1-head.jpg'>Abc</a>";
		//d::dumpas(htmlspecialchars(GW_Link_Helper::trackingLink($message)));
		
		$message = GW_Link_Helper::trackingLink($message);
		
		
		foreach($recipients as $recipient){
			
			$msg = $message;
			$subj = $item->subject;
			
			$this->__prepareMessage($msg, $item, $linkbase, $recipient);
			

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
		
		//d::dumpas($msg);
		
		return ['sent_count'=>$sent_count, 'sent_info'=>$sent_info];
	}
	
	
	function doTest()
	{
		if(! $item = $this->getDataObjectById())
			return false;		
		
		$mail = $_REQUEST['mail'];
		
		GW::getInstance('GW_Config')->set('newsletter/lastmail', $mail);
		
		if(!($recipient=GW::getInstance('GW_NL_Subscriber')->find(['email=?', $mail])))
			$recipient = (object)['id'=>-1,'name'=>'Testname', 'surname'=>'Testsurname', 'email'=>$mail,'lang'=>'lt'];
		
		$info = $this->__doSend($item, [$recipient]);
		
		if($info['sent_count']) {
			$this->app->setMessage('Testinis laiškas išsiųstas į: '.$mail);
		} else {
			$this->app->setMessage('Testinis laiško siuntimas nepavyko. Gavėjas: '.$mail);
		}
		$this->jump();
	}
	
	function viewHits()
	{		
		if(! $item = $this->getDataObjectById())
			return false;
		
		$cond = "message_id=".(int)$item->id;
		$this->setListParams($cond, $params);
		
		
		$params['key_field']='id';
		
		$list = GW::getInstance('GW_NL_Hit')->findAll($cond, $params);
		
		if($this->list_params['page_by'])
			$this->tpl_vars['query_info']=$this->model->lastRequestInfo();		
		
		$this->__addSubscribers($list);
		return ['list'=>$list];
	}
	
	function __addSubscribers(&$list)
	{
		#attach counts
		$subscriber_ids = [];
		foreach($list as $item)
			$subscriber_ids[]=$item->subscriber_id;
		
		$subscribers = GW::getInstance('GW_NL_Subscriber')->findAll( GW_DB::inCondition('id', $subscriber_ids),['key_field'=>'id']);		
		
		
		foreach($list as $item)
			if(isset($subscribers[$item->subscriber_id]))
				$item->subscriber = $subscribers[$item->subscriber_id];
	}	
	
}
