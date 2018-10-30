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

		if(isset($item))
			if($item->extensions['attachments'])
				$item->extensions['attachments']->prepareList($list);
			
	}
	
	function __eventBeforeSave($item)
	{
		 
		$item->updateRecipientsCount();
				
		//paskutini p taga nuimti
		$item->body = preg_replace('/<p>[^\da-z]{0,20}&nbsp;[^\da-z]{0,20}<\/p>/iUs', '', $item->body);	
	}
	
	
	
	function __eventAfterSave($item)
	{
		//surasti linkus irasyt i duombaze
		$body = $item->body;
		
		

		//butina atnaujinti, kitu atveju pakeitimai neisigalios
		$this->prepareBody($item, true);
		
		//$item->__processRecipients($item);
		
		
		//$item->update(['body_prepared']);
	}
	
	function __eventAfterInvertActive($item)
	{
		//boost send
		if($item->active==1 && $item->status==10){
			Navigator::backgroundRequest('admin/lt/emails/messages?act=doSendBackground');	
		}
	}
	
	
	function prepareBody($item, $force = false)
	{
		if(!$force && ($val = GW_Temp_Data::singleton()->readValue(GW_USER_SYSTEM_ID, GW_NL_Message::singleton()->table, $item->id.'/prepbody'))){
			return json_decode($val, true);
		}else{
			$bodyLn = [];
			
			foreach($item->getActiveLangs() as $ln){
			
				$body = $item->get('body', $ln);
				$orig_links = GW_Link_Helper::getLinks($body);
				$links = GW_Link_Helper::cleanAmps($orig_links, $body);


				GW::getInstance('GW_NL_Link')->storeNew($links, $item->id);

				$links = GW::getInstance('GW_NL_Link')->getAllidLink($item->id);

				foreach($links as $id => $link){
					$body = str_replace("'".$link."'", '\'{$TRACKINK_LINK}'.$id."'", $body);
					$body = str_replace('"'.$link.'"', '"{$TRACKINK_LINK}'.$id.'"', $body);
				}
				$bodyLn[$ln] = $body;
			}
			
			GW_Temp_Data::singleton()->store(GW_USER_SYSTEM_ID, GW_NL_Message::singleton()->table, $item->id.'/prepbody', json_encode($bodyLn));
			
			return $bodyLn;
		}		
	}
	
	
	function __eventBeforeClone($item)
	{
		$item->status = 0; 
		$item->active = 0; //leave not active, for security, to leave user as double confirmation or treat that as secure send button
	}
	
	

	
	function doSend()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		if($item->status==0){
			$item->sent_count = 0;
			$item->saveValues(['status'=>10]);
		}
		
		Navigator::backgroundRequest('admin/lt/emails/messages?act=doSendBackground');	
		
		$this->jump();
	}
	
	
	function doSendBackground()
	{
		if($item = $this->model->find('status=10 AND active=1'))
		{
			$info = $this->__sendPortion($item);
			$stat = json_encode($info);
		}else{
			$stat = "Nothing to send";
		}
		
		$this->config->last_background_exec = date('Y-m-d H:i') . ' - '.$stat;
		
		if($item){
			sleep(5);
			Navigator::backgroundRequest('admin/lt/emails/messages?act=doSendBackground');			
		}
		
		exit;			
	}
	
	
	function __sendPortion($item)
	{		
		
		$portionSz = $this->config->portion_size ?? 50;
		$recip_count = 0;
		
		$bodyLn = $this->prepareBody($item);
		
		foreach($item->getActiveLangs() as $ln)
		{
			$recipients = $item->getRecipients($portionSz, $ln);

			$finished = false;

			$response = ['total_size'=>$item->recipients_count];
			
			

			if($recipients){

				$info = $this->__doSend($item, $recipients, $ln, $bodyLn);

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
		
		$response['sent'] = $recip_count;
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
		$this->smarty->assign('NAME', $recipient->name.($recipient->name && $recipient->surname ? ' ':'').$recipient->surname);

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
	
	
	
	function __addAttachments($item, $mail, $ln)
	{
		$attachments = $item->extensions['attachments']->findAll(["field=?", "attachments_$ln"]);
		$att_succ = 0;
		$att_fail = 0;
		
		foreach($attachments as $attachment){
			if($f=$attachment->attachment){
				//d::ldump([$f->full_filename , $f->original_filename]);
				if($mail->AddAttachment( $f->full_filename , $f->original_filename))
				{
					$att_succ++;
				}else{
					$att_fail++;
				}
			}
		}
		
		return [$att_succ,$att_fail];
			
	}	
	
	function __doSend($item, $recipients, $ln, $bodyLn=false)
	{
		$sent_info = [];
		$sent_count=0;
		
		$linkbase = Navigator::getBase(true).'site/';	
		
		
		if(!$bodyLn)
			$bodyLn = $this->prepareBody($item);
		
		//$message = GW_Link_Helper::trackingLink($bodyLn[$ln]);
		$message = $item->getBodyFull($bodyLn[$ln]);
		
		
		
		
		//$message="<a href='http://www.menuturas.lt/newsletter_images/nl1-head.jpg'>Abc</a>";
		//d::dumpas(htmlspecialchars(GW_Link_Helper::trackingLink($message)));
		
		//2015-07-13 removed
		//
		//$mail = $this->initPhpmailer($item->sender, $item->replyto, $item->subject);
	
		
		$mail = GW_Mail_Helper::initPhpmailer($item->get('sender', $ln));
		$mail->Subject = $item->get('subject', $ln);
		
		
		
		$mail->clearAttachments();
		
		//su ta mintim kad visiem attachmentai vienodi
		list($asucc, $afail) = $this->__addAttachments($item, $mail, $ln);
		
		if($afail){
			$this->setError("Failed attach: $afail");
		}
		
		//d::dumpas($mail);
					
		
		foreach($recipients as $recipient){
			
			$msg = $message;//copy for each recipient
			
			$recipient = (object)$recipient;
			
			$message_info = $this->__prepareMessage($msg, $item, $linkbase, $recipient);
						
			$mail->clearAddresses();
			

			$info = ['subscriber_id'=>$recipient->id, 'message_id'=>$item->id, 'time'=>date('Y-m-d H:i:s')];
			
			$mail->addAddress($recipient->email);
			$mail->msgHTML($msg);
			
			//$this->__addAtachments($item, $mail);
			
		
			//$mail->addCustomHeader("List-Unsubscribe",'<'.$mail->__replyTo.'>, <'.$message_info['unsubscribe_link'].'>');
			
			if(!$mail->send()) {
				
				$info['status']=0;
			}else{
				$info['status']=1;
				$sent_count++;
			}
			
					
			if($mail->ErrorInfo){
				$this->setError("Sending error sender:  '{$mail->ErrorInfo}' Msg:$item->id recipient: $recipient->email");
			}
			
			$sent_info[]=$info;
		}
		
		//d::dumpas($msg);
				
		return ['sent_count'=>$sent_count, 'sent_info'=>$sent_info];
	}
	
	
	function doSendTestEmail()
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

		$cfg['fields']['attachments']='l';
		$cfg['fields']['hits']="L";
		//$cfg['fields']['progress']="L";
		
		
		return $cfg;
	}		
	
	
	
	
	
	function doImportFromIPMC()
	{
		$list = IPMC_Mail_Message::singleton()->findAll();
		$cnt = 0;
		
		$filefields=[
		    "file_1_lt"=>'lt',
		    "file_2_lt"=>'lt',
		    "file_3_lt"=>'lt',
		    "file_1_en"=>'en',
		    "file_2_en"=>'en',
		    "file_3_en"=>'en',
		    "file_1_ru"=>'ru',
		    "file_2_ru"=>'ru',
		    "file_3_ru"=>'ru'
		    ];
		
		$att_fail = 0;
		$att_succ = 0;
		
		foreach($list as $item0)
		{
			$item = new GW_NL_Message();
			
			GW_Array_Helper::copy($item0, $item, [
			    'subject_lt',
			    'subject_ru',
			    'subject_en',
			    'body_lt',
			    'body_en',
			    'body_ru',
			    'insert_time',
			    'update_time',
			    'title'
			    ]);
			
			$recipients = json_decode($item0->recipients_data, true);
			
			$response = $this->app->innerRequest("emails/subscribers/importsimple",[],['jsonrows'=>json_encode($recipients)]);
			
			if(!isset($response->ids))
			{
				d::ldump($response);
			}
			
			$item->recipients_ids = $response->ids;	
			
			$item->sender_lt = $item0->sender;
			$item->sender_en = $item0->sender;
			$item->sender_ru = $item0->sender;
			$item->comments = "Importuota iš 'IPMC laiškai' id: ".$item0->id;
			
			if($item->body_lt)
				$item->lang_lt = 1;
			
			if($item->body_en)
				$item->lang_en = 1;
			
			if($item->body_ru)
				$item->lang_ru = 1;
			
			
			$item->status = 70;
					
			$item->updateRecipientsCount();
			$item->insert();
			
			foreach($filefields as $field => $lang)
			{
				if($item0->get($field)){
					$file = $item0->$field;
									
					if($item->extensions['attachments']->storeAttachment("attachments_$lang", $file->getFilename()))
					{
						$att_succ++;
					}else{
						$att_fail++;
					}
				}
			}			
			
			$cnt++;
		}
		
		$this->setMessage("Transfered items: $cnt. Attachments success: $att_succ, fail: $att_fail");
		$this->jump();
	}


	function doUpdateRecCountsAll()
	{
		foreach($this->model->findAll() as $msg){
			$msg->updateRecipientsCount();
			$msg->updateChanged();
		}
			
	}
	
	function doGetProgessPackets()
	{
		$id=(int)$_GET['id'];
		$msg = GW_NL_Message::singleton()->find(["id=?", $id] ,['select'=>'recipients_total,sent_count,id']);
		
		if(!$msg)
		{
			$this->setError("Message($id) not found");
			goto sFinish;
		}else{
			//$progress = rand(1,99);//test
			$progress = $msg->progress;

			$this->app->addPacket(["action"=>"updateProgress","id"=>"massemail_".$id, "progress"=>$progress]);

			if($progress==100)
			{
				$this->app->addPacket(["action"=>"clearInterval","id"=>"progress_massemail_".$id]);			
			}
		}
		
		sFinish:
			$this->app->outputPackets(true);
	}	
	
	
}
