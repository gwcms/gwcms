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
		 
		$item->recipients_count = $this->__getRecipients($item, 1, true);
		
		//paskutini p taga nuimti
		$item->body = preg_replace('/<p>[^\da-z]{0,20}&nbsp;[^\da-z]{0,20}<\/p>/iUs', '', $item->body);	
	}
	
	function __eventAfterSave($item)
	{
		//surasti linkus irasyt i duombaze
		$body = $item->body;
		
		$orig_links = GW_Link_Helper::getLinks($body);
		$links = GW_Link_Helper::cleanAmps($orig_links, $body);
		

		GW::getInstance('GW_NL_Link')->storeNew($links, $item->id);
		
		$links = GW::getInstance('GW_NL_Link')->getAllidLink($item->id);
		
		foreach($links as $id => $link){
			$body = str_replace("'".$link."'", '\'{$TRACKINK_LINK}'.$id."'", $body);
			$body = str_replace('"'.$link.'"', '"{$TRACKINK_LINK}'.$id.'"', $body);
		}
		
		$item->body_prepared = $body;
		
		
		$item->update(['body_prepared']);
	}
	
	
	
	function __getRecipients($letter, $portion, $count_total=false)
	{
		$db =& $letter->getDB();
		
		$incond = GW_DB::inCondition('b.`group_id`', $letter->groups);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT a.id, a.* 
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
				(a.confirm_code IS NULL OR a.confirm_code < 100) AND
				$incond 
				". (!$count_total ? 'AND aa.status IS NULL' : '')."
			LIMIT $portion
			";
		
		
		
		$sql = GW_DB::prepare_query([$sql, $letter->id, $letter->lang]);
		
		
		
		$rows = $db->fetch_rows($sql);
		
		
		if($count_total){
			
			$count_total= $db->fetch_result("SELECT FOUND_ROWS()");
			
			return $count_total;
		}
		
		
		
		
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
		
		$info = $this->__sendPortion($item);
		
		
		echo json_encode($info);	
		exit;
	}
	
	
	function __sendPortion($item)
	{		
		$recipients = $this->__getRecipients($item, $this->config->portion_size);
		
		$finished = false;
		
		$response = ['total_size'=>$item->recipients_count];
		
		if(!$recipients)
		{
			$item->saveVAlues(['status'=>70, 'sent_time'=>date('Y-m-d H:i:s')]);
			
			$response['finished']=true;

		}else{
				
			$info = $this->__doSend($item, $recipients);

			$item->getDB()->multi_insert('gw_nl_sent_messages', $info['sent_info']);

			$item->saveVAlues(['sent_count'=>$item->sent_count + count($recipients)]);
			
			
			$response['portion_sent']=$info['sent_count'];
			$response['portion_size']=count($recipients);
		}
		
		$response['total_sent']=$item->sent_count;
		
		return $response;
	}
	
	function viewSentInfo()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		
		$options=['joins'=>[['INNER','gw_nl_sent_messages AS ca','ca.subscriber_id = a.id AND ca.message_id='.(int)$item->id]]];
		$options['select']='a.*, ca.status, ca.time';
		$options['order']='ca.time DESC';
				
		$this->setListParams($options);
		
		$list = GW::getInstance('GW_NL_Subscriber')->findAll($options['conditions'], $options);
		
		
		
		
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
		$us_link=$us_link.urlencode($reml_encoded);
		$us_link_html='<a href="'.$us_link.'">'.$this->lang['LINK'][strtoupper($recipient->lang)].'</a>';
		
		$this->smarty->assign('UNSUBSCRIBE', $us_link_html);
		
		$link_online = $linkbase.'item?nlid='.$letter->id.'&rid='.$recipient->id.'&re='.$reml_encoded;
		$link_online = '<a href="'.$link_online.'">'.$this->lang['LINK'][strtoupper($recipient->lang)].'</a>';
		$this->smarty->assign('LINKONLINE', $link_online);
		
		# 3 - TRACKING PRIDEJIMAS
		# //2015-07-13 removed
		//$trackinglink=$linkbase."link?nlid={$letter->id}&rid=".$recipient->id.'&link=';
		$trackinglink2=$linkbase."link2?nlid={$letter->id}&rid=".$recipient->id.'&link=';	
		$this->smarty->assign('TRACKINK_LINK', $trackinglink2);
		
		$msg = $this->smarty->fetch('string:'.$msg);		
		
		//2015-07-13 removed
		//$msg = str_replace('##TRACKINGLINK##', $trackinglink, $msg);
		
		return ['unsubscribe_link'=>$us_link];
	}
	
	
	function __doSend($item, $recipients)
	{
		$sent_info = [];
		$sent_count=0;
		
		$linkbase = Navigator::getBase(true).'site/';	
		
		$message = $item->getBodyFull('body_prepared');
		
		//$message="<a href='http://www.menuturas.lt/newsletter_images/nl1-head.jpg'>Abc</a>";
		//d::dumpas(htmlspecialchars(GW_Link_Helper::trackingLink($message)));
		
		//2015-07-13 removed
		//$message = GW_Link_Helper::trackingLink($message);
		$mail = $this->initPhpmailer($item->sender, $item->replyto, $item->subject);
		
		
		foreach($recipients as $recipient){
			
			$msg = $message;

			$recipient = (object)$recipient;
			
			$message_info = $this->__prepareMessage($msg, $item, $linkbase, $recipient);
			

			$info = ['subscriber_id'=>$recipient->id, 'message_id'=>$item->id, 'time'=>date('Y-m-d H:i:s')];
			
			$mail->addAddress($recipient->email);
			$mail->msgHTML($msg);
			
		
			//$mail->addCustomHeader("List-Unsubscribe",'<'.$mail->__replyTo.'>, <'.$message_info['unsubscribe_link'].'>');
			
			
			if(!$mail->send()) {
				$info['status']=0;
			}else{
				$info['status']=1;
				$sent_count++;
			}
			
			$mail->clearAddresses();
			$mail->clearAttachments();
			
	
			
			
			
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
		
		$params['joins']=array(['left','gw_nl_subscribers AS aa','a.subscriber_id=aa.id']);
		$params['select']='a.*, aa.id AS subscriber_id, aa.email AS email';
		
		$list = GW::getInstance('GW_NL_Hit')->findAll($cond, $params);
		
		if($this->list_params['page_by'])
			$this->tpl_vars['query_info']=$this->model->lastRequestInfo();		
		
		//$this->__addSubscribers($list);
		return ['list'=>$list, 'links'=> GW::getInstance('GW_NL_Link')->getAllidLink($item->id)];
	}
	
	
	function initPhpmailer($from, $replyto, $subject)
	{
		$mail = new PHPMailer;
		//$mail->isSendmail();
		$mail->CharSet = 'UTF-8';
		
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($from);
		$mail->setFrom($email, $name);
		
		list($name, $email) = GW_Email_Validator::separateDisplayNameEmail($replyto);
		$mail->addReplyTo($email, $name);
		$mail->__replyTo = $email;
		
		$mail->Subject = $subject;
		
		$mail->DKIM_domain = $this->config->dkim_domain;
		$mail->DKIM_private = GW::s('DIR/SYS_FILES').'.mail.key';

		$mail->DKIM_selector = $this->config->dkim_domain_selector;
		$mail->DKIM_passphrase = ''; //key is not encrypted
		
		return $mail;
	}
	
	
	function viewTestPhpMailer()
	{
		
		$mail = new PHPMailer;
		// Set PHPMailer to use the sendmail transport
		//$mail->isSendmail();
		
		$mail->addAddress('<vidmantas.work@gmail.com>', 'Vidmantas Darbinis');
		
		$mail->setFrom('postmaster@lektuvu.lt', 'PostMasteris at lektuvu');
		//Set an alternative reply-to address


		$mail->addAddress('laiskonoriu@gmail.com', 'Pirmas android akountas');
		//Set the subject line
		$mail->Subject = 'PHPMailer - DKIM tikrinu 2';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML("tai yra <b>bandymas</b>");
		//Replace the plain text body with one created manually
		$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');



		
		

		//send the message, check for errors
		if (!$mail->send()) {
		    echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		    echo "Message sent!";
		}		
		
	}
	
	
	function viewSend()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		
		//d::dumpas('tikrinu');
		return ['id'=>$item->id ];
	}
	
}
