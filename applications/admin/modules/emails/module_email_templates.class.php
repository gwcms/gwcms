<?php


class Module_Email_Templates extends GW_Common_Module
{	

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
		
		$cfg = array('fields' => []);
		
		

		
		
		$cfg["fields"]["id"]="lof";
		
		$cfg["fields"]["title"]="Lof";
		$cfg["fields"]["subject"]="Lof";
		$cfg["fields"]["body"]="lof";
		$cfg["fields"]["owner_type"]="Lof";
		$cfg["fields"]["owner_field"]="Lof";
		
		$cfg["fields"]["ln_enabled"]="lof";
		$cfg["fields"]["body"]="lof";
		$cfg["fields"]["body"]="lof";
		
		$cfg["fields"]['insert_time'] = 'lof';
		$cfg["fields"]['update_time'] = 'lof';
		//$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	


	function viewOptions()
	{
		$cond = GW_DB::buidConditions($this->filters);
		
		if(isset($_GET['q'])){
			$condsearch = 'admin_title LIKE "%'.GW_DB::escape($_GET['q']).'%"';
			$cond = GW_DB::mergeConditions(GW_DB::prepare_query($cond), $condsearch);
		}
		
		
		if(isset($_GET['byid'])){
			$opts = $this->model->getOptionsByID($cond);
		}else{
			$opts = $this->model->getOptions($cond);
		}
		
		$list = [];
		foreach($opts as $id => $title){
			$list[] =  ["id"=>$id, "title"=>$title];
		}
				
		echo json_encode(["items"=>$list, "total_count"=>count($opts)]);
		exit;
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
		$filename=GW::s('DIR/SYS_REPOSITORY').'testpdfhtml.html';
		
		if($_POST)
		{
			file_put_contents($filename, $_POST['item']['htmlcontents']);
		}
		
		$this->tpl_vars['filecontents'] = @file_get_contents($filename);
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
	
	
	
}
