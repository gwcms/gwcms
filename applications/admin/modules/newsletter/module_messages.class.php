<?php


class Module_Messages extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
		
		$this->config = new GW_Config($this->module_path[0].'/');
		
	}

	function __eventAfterForm()
	{
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptionsWithCounts(false);		
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
	
	
	
	function __getRecipients($letter, $portion)
	{
		$db =& $letter->getDB();
		
		$incond = GW_DB::inCondition('b.`group_id`', $letter->groups);
		
		$sql = "SELECT DISTINCT a.id, a.* 
			FROM 
				`gw_nl_subscribers` AS a
			INNER JOIN `gw_nl_subs_bind_groups` AS b
				ON a.id=b.subscriber_id
			LEFT JOIN gw_nl_sent_messages AS aa 
				ON a.id = aa.subscriber_id AND aa.message_id=?
			WHERE 
				a.unsubscribed=0 AND
				a.active=1 AND 
				a.lang=? AND
				$incond AND
				aa.status IS NULL
			LIMIT $portion
			";
		
		$sql = GW_DB::prepare_query([$sql, $letter->id, $letter->lang]);	
		$rows = $db->fetch_rows($sql);
		
		return $rows;
	}
	
	function doSend()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		if($item->status==0){
			$item->sent_count = 0;
			$item->saveValues(['status'=>10]);
		}
		
		$finished = $this->__sendPortion($item);
			

		
		if($finished)
		{
			$this->jump($this->app->path.'/sentinfo');	
		}
	}
	
	
	function __sendPortion($item)
	{		
		$recipients = $this->__getRecipients($item, $this->config->portion_size);
		
		if(!$recipients)
		{
			$item->setValues(['status'=>70, 'sent_time'=>date('Y-m-d H:i:s')]);
			return true;
		}
				
		$info = $this->__doSend($item, $recipients);
		
		
		$item->getDB()->multi_insert('gw_nl_sent_messages', $info['sent_info']);
		
		$item->saveVAlues(['sent_count'=>$item->sent_count + count($recipients)]);
		
		
		$this->app->setMessage($this->lang['SENT'].$info['sent_count'].'/'.count($recipients));
			
		
	}
	
	function viewSentInfo()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		$cond='1=1';
		
		$options=['joins'=>[['INNER','gw_nl_sent_messages AS ca','ca.subscriber_id = a.id AND ca.message_id='.(int)$item->id]]];
		$options['select']='a.*, ca.status, ca.time';
		$options['order']='ca.time DESC';
		
		$this->setListParams($cond, $options);
		
		$list = GW::getInstance('GW_NL_Subscriber')->findAll($cond, $options);
		
		
		
		if($this->list_params['page_by'])
			$this->tpl_vars['query_info']=$this->model->lastRequestInfo();		
		
		
		return ['list'=>$list];
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
			$recipient = (object)$recipient;
			
			$this->__prepareMessage($msg, $item, $linkbase, $recipient);
			

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			
			if($item->sender)
				$headers .= "From: $item->sender\r\n";

			$info = ['subscriber_id'=>$recipient->id, 'message_id'=>$item->id, 'time'=>date('Y-m-d H:i:s')];
			
			
			if(!mail($recipient->email, $subj, $msg, $headers)) {
				$info['status']=0;
			}else{
				$info['status']=1;
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
