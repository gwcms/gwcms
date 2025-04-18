<?php


class Module_Email_Templates extends GW_Common_Module
{	

	use Module_Import_Export_Trait;		
	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		$this->app->carry_params['owner_type']=1;
		$this->app->carry_params['clean']=1;
		
		
		if(isset($_GET['owner_type']))
		{
			$this->filters['owner_type'] = $_GET['owner_type'];
		}
		
		if(isset($_GET['owner_field']))
		{
			$this->filters['owner_field'] = $_GET['owner_field'];
		}
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
		parent::eventHandler($event, $context);
	}
	
	function __addAtachments($item, $mail, $lang)
	{
		for($i=1;$i<=3;$i++)
			if($f=$item->get("file_".$i."_".$lang))		
				$mail->AddAttachment( $f->full_filename , $f->original_filename);
			
	}
	
	function doSend()
	{
		if(! $item = $this->getDataObjectById())
			return false;
		
		$recip = json_decode($item->recipients_data);
		
		$sent_count = $item->sent_count;
		
		$mail = GW_Mail_Helper::initPhpmailer($item->sender);

		
		foreach($recip as $recipient)
		{
			$this->__addAtachments($item, $mail, $recipient->lang);
			
			$msg = $item->{"body_".$recipient->lang};
			$msg = str_replace('%NAME%', $recipient->name, $msg);
			
	
			$mail->Subject = $item->{"subject_".$recipient->lang};
			$mail->addAddress($recipient->email);
			$mail->msgHTML($msg);

			//d::dumpas([$mail,$mail->send()]);
			
			if(!$mail->send())
			{
				$this->setError("Įvyko klaida. Nepavyksta išsiųsti į $recipient->email");
				$recipient->sent = false;
			}else{
				$this->app->setMessage("$recipient->name &gt; $recipient->email :: $recipient->lang :: OK");
				$recipient->sent=true;
				$sent_count++;
			}
			
			$mail->ClearAllRecipients( );
			$mail->clearAttachments();//jei atskiram useriui atskiras attach
		}
		
		$item->saveValues(['sent_info'=>  json_encode($recip), 'sent_count'=>$sent_count]);
		
		$this->jump();
	}
	
	
	function getListConfig()
	{
		
		//d::dumpas();
		
		$cfg = parent::getListConfig();
		

		foreach($cfg["fields"] as $field => $sett)
			$cfg["fields"][$field]='lof';

		
		
		$cfg["fields"]["id"]="lof";
		
		$cfg["fields"]["title"]="Lof";
		$cfg["fields"]["idname"]="Lof";
		$cfg["fields"]["subject"]="Lof";
		$cfg["fields"]["owner_type"]="Lof";
		$cfg["fields"]["owner_field"]="Lof";
		
		$cfg["fields"]["ln_enabled"]="lof";
		$cfg["fields"]["body"]="lof";
		
		$cfg["fields"]['insert_time'] = 'lof';
		$cfg["fields"]['update_time'] = 'lof';
		//$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	



	

	function getOptionsCfg()
	{
		$opts = [
			'title_func'=>function($o){ return $o->id.'. '.$o->admin_title; },
			'search_fields'=>['id', 'admin_title'],
		];
		 
		$opts['condition_add']='1=1';    
		
		    
		if(isset($_GET['owner_type']))
			$opts['condition_add'] .= GW_DB::prepare_query([' AND  owner_type=?', $_GET['owner_type']]);
		if(isset($_GET['owner_field']))
			$opts['condition_add'] .= GW_DB::prepare_query([' AND  owner_field=?', $_GET['owner_field']]);
		
		return $opts;	
	}
		
	
	function __eventAfterForm($item)
	{
		$owner_type = $this->filters['owner_type'] ?? '';
		$owner_field = $this->filters['owner_field'] ?? '';
		$cfg = $this->app->sess("email_templates/{$owner_type}/{$owner_field}/cfg");
		
		$name = $_GET['name'] ?? '';
		$default_vals = $this->app->sess("email_templates/{$owner_type}/{$owner_field}/{$name}/default_vals");
		
		//d::dumpas($cfg);
		$saved_cfg = json_decode($item->config, true);
		
		
		$this->tpl_vars['custom_cfg'] = array_merge(is_array($cfg) ? $cfg: [], is_array($saved_cfg) ? $saved_cfg :[]);		
		
		//d::dumpas($cfg);
		//it is posible to predefine some vals
		if(isset($cfg['vals']))
			$item->setValues($cfg['vals']);
		
		if($default_vals && is_array($default_vals))
			if(!$item->id)
				$item->setValues($default_vals);
			
		
		$item->setValues($this->filters);		
					
	}
	
	
	function viewTestPdfGen()
	{
		if(($item = $this->getDataObjectById()) && !$_POST)
		{
			$this->tpl_vars['filecontents'] = $item->body_lt;
		}else{
			$filename=GW::s('DIR/SYS_REPOSITORY').'testpdfhtml.html';

			if($_POST)
			{
				file_put_contents($filename, $_POST['item']['htmlcontents']);
			}

			$this->tpl_vars['filecontents'] = @file_get_contents($filename);
		}
	}		
	
	function doGenPdf()
	{
		$filename=GW::s('DIR/SYS_REPOSITORY').'testpdfhtml.html';
		
		$pdf=GW_html2pdf_Helper::convert(file_get_contents($filename), false);
		header("Content-type:application/pdf");
		header("Content-Disposition:inline;filename=test.pdf");
		die($pdf);		
	}
	
	
	
	function __eventBeforeDelete($item)
	{
		if($item->protected)
		{
			$this->setError("Cant delete protected item");
		}
		
	}
	
	//function __eventAfterForm()
	//{
	//	d::dumpas('test');
		
	//}
	
	function doTest()
	{
		d::dumpas('test');
	}
	
	
	
	function doSendTest()
	{
		$item = $this->getDataObjectById();
		
		
		
		$opts = [
		    'to'=>$_GET['email'],
		    'subject'=>$item->subject_lt ?: "email test, template id:".$item->id,
		    'body'=> $item->body_lt
		];


		$x = GW_Mail_Helper::sendMail($opts);
		$this->setMessage("Mail test sent to '{$_GET['email']}'");
		$this->jump();
	}
	
	function viewItemJson()
	{
		$item = $this->getDataObjectById();
		
		echo json_encode($item->toArray());
		exit;
	}
	
	function __eventBeforeSave0($item)
	{

		//visi pabargo ivedinet idname, gal ir nebutina is admin title sudaryt
		if(!$item->idname && $item->admin_title){
			$item->idname = FH::urlStr($item->admin_title).'_'.date('Ymd_His');
		}	
	}	
	
}
