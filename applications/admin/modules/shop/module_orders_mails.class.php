<?php



class Module_Orders_Mails extends GW_Module_Extension
{		
	function viewMailS2()
	{
		$this->initListParams(false, "list");
		$vars = $this->viewList();
		
		if(isset($this->app->sess['mailsend_cfg'])){
			$vars['item'] = (object)$this->app->sess['mailsend_cfg'];
			unset($this->app->sess['mailsend_cfg']);
		}
		
		if(isset($vars['item']->template_id))
		{
			$vars['item']->template=GW_Mail_Template::singleton()->createNewObject($vars['item']->template_id, 1);	
		}
		
		return $vars;
	}
	
	function getMailOrder($id)
	{
		$client = Shop_Orders::singleton()->find(['id=?', $id]);
		
		return $client;
	}
	
	function doMailS2()
	{
		//d::dumpas('abc');
		$vals = $_POST['item'];
		$recrows = $vals['recipients'];
		$recrows = explode("\n", $recrows);
		$error = 0;
		
		
		
		foreach($recrows as $row0){
			if(!$row0)
				continue;
			
			$row = explode(";", $row0);
			
			if(count($row) != 3){
				$this->setError("Problem with row: \"$row0\"");
				
				$error = 1;
			}
			
			$recipient = $this->getMailParticipant($row[0]);
			
			
			if(!$recipient){
				$this->setError("Nerastas dalyvis pagal id $row[0]");
				$error = 1;				
			}
				
			list($id,$lang,$to) = $row;
			
			$recipients[] =$recipient;
			$recipient_rows[]  = ['id'=>$id, 'to'=>$to, 'lang'=>$lang];
			
		}
		
		if(($vals['template_id'] ?? false) == false)
		{
			$this->setError("Nenurodytas laiško šablonas");
			$error = 1;
		}
		
		if(!count($recipients))
		{
			$this->setError("Nenurodyti gavėjai");
			$error = 1;
		}	
		
		$vals['confirm'] = 1;
		$vals['recipient_rows'] = $recipient_rows;
		$this->app->sess['mailsend_cfg'] = $vals;	
		
		
		//d::Dumpas($vals);
		
		if(isset($vals['confirmsend']))
		{
			if($vals['confirmsend']!='yes'){
				$this->setErrors('Must confirm');
			}else{
				
				$this->doSendS2($vals['template_id'], $vals['recipient_rows']);
			}
		}
		
		if($error)	
			$this->jump();
		
		
	}
	
	function doSendS2($tpl_id, $rows)
	{
		$status = []; 
		$template = GW_Mail_Template::singleton()->createNewObject($tpl_id, 1);
		$succcnt=0;
		$failed = [];
		$total = count($rows);
			
		
		foreach($rows as $recip){	
			
			$order = $this->getMailOrder($recip['id']);

			
			$opts = $this->prepareMail($order, $recip['to'], $recip['lang'], $template);
			
			if(GW_Mail_Helper::sendMail($opts)){
				$succcnt++;
			}else{
				$failed[] = $recip['to'];
			}
		}

		if($succcnt)
			$this->setMessage("Išsiųsta: $succcnt / $total");
		
		if($failed)
			$this->setErrors("Nepavyko išsiųsti: ". htmlspecialchars(implode('; ', $failed)));
		
		$this->jump();
	}
	
	
	function doMailPreview()
	{
		$recip =  $_GET['row'];

		$order = $this->getMailOrder($recip['id']);
		
		$vars = $this->prepareMail($order, $recip['to'], $recip['lang'], $_GET['template_id']);
		

		$this->smarty->assign('data', $vars);
		$this->tpl_file_name = $this->tpl_dir."mails2preview";		
		$this->processTemplate();		
	}
	
	function viewMailS1()
	{
		return ['item' => $this->getDataObjectById()];
		//d::dumpas($this->tpl_vars['item']->recipient);
		
	}	
	
	function prepareMail($order, $to, $ln, $template_id)
	{				

		$from = '';
		
		if($template_id){
			$vars = $this->getMailVars($order, $ln);

			$tpl = is_object($template_id) ? $template_id :  GW_Mail_Template::singleton()->find(['id=?', $template_id]);
			
			$body = GW_Mail_Helper::prepareSmartyCode($tpl->get('body', $ln), $vars);
			$subject = GW_Mail_Helper::prepareSmartyCode($tpl->get('subject', $ln), $vars);

			if($tpl->custom_sender){	
				$from = $tpl->get('sender', $ln);
			}	
		}else{
			$body = "";
			$subject = "";
		}

		return [
		    'to'=>$to,
		    'from'=>$from,
		    'body' => $body,
		    'subject' => $subject,
		];		
	}
	
	//fill template and pass it to email_queue/form
	function doMailS1()
	{
		$data = $_POST['item'];
		//prepare tempalte:
		
		
		$ln = $data["use_lang"];
		$to =  $data["recipient"];
		$order = $this->getDataObjectById();
		
		$this->app->sess['item']= $this->prepareMail($order, $to, $ln, $data['template_id']);
		
		$this->app->jump('emails/email_queue/form?id=0');
		
		/*
		d::ldump($tpl);
		d::ldump($vars);
		d::ldump($subject);
		d::ldump($body);
		d::dumpas($_POST);
		*/
	}
	

}
