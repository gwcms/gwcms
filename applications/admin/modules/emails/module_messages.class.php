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

	function __eventAfterForm($item)
	{
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptionsWithCounts(false);
		//d::ldump($item->recipients_ids);
		//d::dumpas($item);
		
		//d::dumpas($item);
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
		$this->tpl_vars['lasttestmail']=GW::getInstance('GW_Config')->get('emails/lastmail');
		
		$this->__addHitCounts($list);

		//to get first item
		foreach($list as $item)
			break;

		if($item)
			if($item->extensions['attachments'])
				$item->extensions['attachments']->prepareList($list);
			
	}
	
	function __eventBeforeSave($item)
	{
		 
		foreach($item->getActiveLangs() as $ln){
	
			
			
			$item->set('recipients_count', $this->__getRecipients($item, 1, $ln, true), $ln);
		}
		
		

		
		//paskutini p taga nuimti
		$item->body = preg_replace('/<p>[^\da-z]{0,20}&nbsp;[^\da-z]{0,20}<\/p>/iUs', '', $item->body);	
	}
	
	
	function __parseRecipients($text, $lang, &$list)
	{
		$text = str_replace("\t",'', $text);
		
		$recipients = explode("\n", $text);
		
		foreach($recipients as $recipient)
		{
			$tmp = explode(';', $recipient);;
			if(count($tmp)==2)
				$list[] = ['name'=>$tmp[0], 'email'=>$tmp[1], 'lang'=>$lang];
		}
	}
	
	function beforeSaveParseRecipients()
	{
		$recipients=[];
		$this->parseRecipients($this->recipients_lt, 'lt', $recipients);
		$this->parseRecipients($this->recipients_en, 'en', $recipients);
		$this->parseRecipients($this->recipients_ru, 'ru', $recipients);

		//d::dumpas($recipients);

		$this->recipients_count = count($recipients);
		$this->recipients_data = json_encode($recipients);
	}	
	
	
	function __processRecipients($item)
	{
		
	}
	
	
	
	function __eventAfterSave($item)
	{
		//surasti linkus irasyt i duombaze
		$body = $item->body;
		
		
		/*
		$orig_links = GW_Link_Helper::getLinks($body);
		$links = GW_Link_Helper::cleanAmps($orig_links, $body);
		

		GW::getInstance('GW_NL_Link')->storeNew($links, $item->id);
		
		$links = GW::getInstance('GW_NL_Link')->getAllidLink($item->id);
		
		foreach($links as $id => $link){
			$body = str_replace("'".$link."'", '\'{$TRACKINK_LINK}'.$id."'", $body);
			$body = str_replace('"'.$link.'"', '"{$TRACKINK_LINK}'.$id.'"', $body);
		}
		*/
		//$item->body_prepared = $body;
		
		//$item->__processRecipients($item);
		
		
		//$item->update(['body_prepared']);
	}
	
	
	
	function __getRecipients($letter, $portion,  $lang, $count_total=false)
	{
		$db =& $letter->getDB();
		
		$grp_cond = " FALSE ";
		
		if($letter->groups){
			$grp_incond = GW_DB::inCondition('b.`group_id`', $letter->groups);
			$grp_cond = "a.active=1 AND (a.confirm_code IS NULL OR a.confirm_code < 100) AND $grp_incond";
		}
		
		
		$separete_ids = $letter->recipients_ids;
		$part_incond = " FALSE ";
		
		if($separete_ids){
			
			$part_incond = GW_DB::inCondition('a.`id`', $separete_ids);
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT a.id, a.* 
			FROM 
				`gw_nl_subscribers` AS a
			LEFT JOIN `gw_nl_subs_bind_groups` AS b
				ON a.id=b.subscriber_id
			LEFT JOIN gw_nl_sent_messages AS aa 
				ON a.id = aa.subscriber_id AND aa.message_id=?
			WHERE 
				a.unsubscribed=0 AND
				a.lang=? AND
				( ($grp_cond) OR ($part_incond) )
				". (!$count_total ? 'AND aa.status IS NULL' : '')."
			LIMIT $portion
			";
		
		$sql = GW_DB::prepare_query([$sql, $letter->id, $lang]);
		
		$rows = $db->fetch_rows($sql);
		
		//d::dumpas([$rows, $sql, $lang]);
		
		
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
	
	
	function doSendBackground()
	{
		//
	}
	
	
	function __sendPortion($item)
	{		
		
		$portionSz = $this->config->portion_size ?? 50;
		$recip_count = 0;
		
		foreach($item->getActiveLangs() as $ln)
		{
			$recipients = $this->__getRecipients($item, $portionSz, $ln);

			$finished = false;

			$response = ['total_size'=>$item->recipients_count];
			
			

			if($recipients){

				$info = $this->__doSend($item, $recipients, $ln);

				$item->getDB()->multi_insert('gw_nl_sent_messages', $info['sent_info']);

				$item->saveVAlues(['sent_count'=>$item->sent_count + count($recipients)]);


				$response[$ln]['portion_sent']=$info['sent_count'];
				$response[$ln]['portion_size']=count($recipients);
				
				$recip_count += count($recipients);
			}
		}
		
		
		if($recip_count==0)
		{
			$item->saveVAlues(['status'=>70, 'sent_time'=>date('Y-m-d H:i:s')]);

			$response['finished']=true;			
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
	
	
	
	function __addAtachments($item, $mail)
	{
		for($i=1;$i<=1;$i++)
			if($f=$item->get("file_".$i))		
				$mail->AddAttachment( $f->full_filename , $f->original_filename);
			
	}	
	
	function __doSend($item, $recipients, $ln)
	{
		$sent_info = [];
		$sent_count=0;
		
		$linkbase = Navigator::getBase(true).'site/';	
		
		$message = $item->getBodyFull("body_$ln");
		
		//$message="<a href='http://www.menuturas.lt/newsletter_images/nl1-head.jpg'>Abc</a>";
		//d::dumpas(htmlspecialchars(GW_Link_Helper::trackingLink($message)));
		
		//2015-07-13 removed
		//$message = GW_Link_Helper::trackingLink($message);
		//$mail = $this->initPhpmailer($item->sender, $item->replyto, $item->subject);
	
		
		$mail = GW_Mail_Helper::initPhpmailer($item->get('sender', $ln));
		$mail->Subject = $item->get('subject', $ln);
					
		
		foreach($recipients as $recipient){
			
			$msg = $message;

			$recipient = (object)$recipient;
			
			$message_info = $this->__prepareMessage($msg, $item, $linkbase, $recipient);
			

			$info = ['subscriber_id'=>$recipient->id, 'message_id'=>$item->id, 'time'=>date('Y-m-d H:i:s')];
			
			$mail->addAddress($recipient->email);
			$mail->msgHTML($msg);
			
			$this->__addAtachments($item, $mail);
			
		
			//$mail->addCustomHeader("List-Unsubscribe",'<'.$mail->__replyTo.'>, <'.$message_info['unsubscribe_link'].'>');
			
			
			if(!$mail->send()) {
				
				$info['status']=0;
			}else{
				d::ldump([$mail]);
				$info['status']=1;
				$sent_count++;
			}
			
			$mail->clearAddresses();
			$mail->clearAttachments();
					
			if($mail->ErrorInfo){
				$this->setError("Sending error sender:  '{$mail->ErrorInfo}' Msg:$item->id recipient: $recipient->email");
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
		
		foreach($item->getActiveLangs() as $ln){
			$info = $this->__doSend($item, [$recipient], $ln);

			if($info['sent_count']) {
				$this->setPlainMessage("$ln: Testinis laiškas išsiųstas į: $mail");
			} else {
				$this->setPlainMessage("$ln: Testinis laiško siuntimas nepavyko. Gavėjas: $mail");
			}
		}
		$this->jump();
	}
	
	function viewHits()
	{		
		if(! $item = $this->getDataObjectById())
			return false;
		
		$params['conditions'] = "message_id=".(int)$item->id;
		$this->setListParams($params);
		
		
		$params['key_field']='id';
		
		$params['joins']=array(['left','gw_nl_subscribers AS aa','a.subscriber_id=aa.id']);
		$params['select']='a.*, aa.id AS subscriber_id, aa.email AS email';
		
		$list = GW::getInstance('GW_NL_Hit')->findAll($params['conditions'], $params);
		
		if($this->list_params['page_by'])
			$this->tpl_vars['query_info']=$this->model->lastRequestInfo();		
		
		//$this->__addSubscribers($list);
		return ['list'=>$list, 'links'=> GW::getInstance('GW_NL_Link')->getAllidLink($item->id)];
	}
	
	
	function initPhpmailer($from, $replyto, $subject)
	{
		
		$mail = GW::getInstance('phpmailer',GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php');
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
		
		$mail = GW::getInstance('phpmailer',GW::s('DIR/VENDOR').'phpmailer/phpmailer.class.php');
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
	
	

	
	
	function viewModInfo()
	{
		
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		$cfg['fields']['recipients_count']='l';

		return $cfg;
	}		
	
}
