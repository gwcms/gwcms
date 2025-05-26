<?php



class Module_Inbox extends GW_Common_Module
{	

	/**
	 *
	 * @var Itax
	 */
	public $itax;
	
	function init()
	{		
		
		$this->lgr = new GW_Logger(GW::s('DIR/LOGS').'ticket_mail_import.log');
		$this->lgr->collect_messages = true;
		
		
		$this->initModCfg();
		
		$this->app->carry_params['reservation_code']=1;
		$this->app->carry_params['clean']=1;
		

		
				
		
		parent::init();
		
		$this->list_params['paging_enabled'] = true;
	}		
	
	function imap()
	{
		$cfg = $this->modconfig;
		return new GW_Imap($cfg->imap_hostport, $cfg->imap_user, $cfg->imap_passenc);		
	}
	
	function getRules()
	{
		$scanrules = json_decode($this->modconfig->rules, true);
		return $scanrules;
	}
	
	function doScanMail()
	{		
		$sys_call = false;
		
		if(isset($_GET['cron'])){
			$sys_call = true;
			if($this->modconfig->imap_scan_enabled==0)
				die('disabled auto scan');
		}
		
		
		$mail = $this->imap();
		$t = new GW_Timer;		
		
		//resend all
		$info=['f'=>__FUNCTION__];
		
		/*
		$scanrules=[
			'HalleonardInvoice'=>[
			    'inbox'=>['from'=>'/intsales@halleonardeurope.com/i', 'subject'=>'Sales Invoice'],
			    'modpath'=>'products/inboxhandler',
			    'attachmentstore'=>'/^Invoice.*\.csv$/i'
			    ],
		];
		 * 
		 */
		
		$scanrules = $this->getRules();
		
		//d::dumpas($scanrules);
		
		$options = [];
		

		$options['limit'] = $this->modconfig->mailfetch_portion ?: 50;
		
		$last = $this->modconfig->last_mailbox_uid ?: 1;
		
		//developinimui pakisti
		if($_GET['last'] ?? false)
			$last = $_GET['last'];
		
		$start = $mail->getIdByUid($last);
		$options['start'] = $start ? $start : 1;
		
		
		//dev
		$options['start'] = 1;
		
		//$options['flagignore']=1;

		$data = $mail->getMessages($scanrules, $options);
		
		//d::dumpas($data);
		
		$info['Time listfetch']=$t->stop();
		$info['last'] = $this->modconfig->last_mailbox_uid;
		
		$this->modconfig->last_mailbox_uid = $data['lastuid'];
		
		if(isset($_GET['debug']))
			d::ldump($data);
		
		$parsefailedcnt=0;
		$duplicates=0;
		
		foreach($data['messages'] as $messageid => $message)
		{
			$this->messageProcess($message, $mail);
		}
		
		foreach($data['messages'] as $msg){
			$newmsg_groups[ $msg->ruleid ] = ($newmsg_groups[ $msg->ruleid ]  ?? 0) +1;
			
			
		}
		
		foreach($newmsg_groups as $grid => $cnt){
			if(isset($scanrules[$grid]['modpath']))
				$url=Navigator::backgroundRequest('admin/lt/'.$scanrules[$grid]['modpath'], ['act'=>'doNewInbox']);
		}
		
		
		
		
		$mail->close();
		
		
		$info['Imported mails'] = count($data['messages'])-$parsefailedcnt;
		$info['Parse failed'] = $parsefailedcnt;
		$info['Scanned items'] = $data['steps'];
		$info['Time'] = $t->stop();
		 
			
		$this->setMessage(json_encode($info));
		$this->modconfig->last_mailbox_read = date('Y-m-d H:i');		
		

		
		
		if($sys_call){
			exit;
		}else{
			$this->jump();
		}
		
		
	}
	
	function messageProcess($message, $mail)
	{
		$vals=[];
		$vals['headers'] = json_encode($message->head);

		$mail->fetchContents($message, 'body');

		$vals['body'] = $message->body;

		$message->body="";//save memory

		$vals['from'] = $message->from;
		$vals['to'] = $message->to;

		$vals['subject'] = $message->subject;
		$vals['orig_time'] = date('Y-m-d H:i:s',$message->head->udate);
		
		
		$vals['ruleid'] = $message->ruleid;
		$vals['mailid'] = $message->uid;
		
		
		$vals['attach_list'] =  isset($message->attachments_structure) ? $message->attachments_structure : [];

		//no duplicates should be created
		$item = $this->model->find(['mailid=?', $message->uid]);
		
		if($item){
			$item->fireEvent('BEFORE_CHANGES');//track changes on
			if($item->subject != $vals['subject'])
				$this->setMessage("found existing and this might be bad situation because subject is different");
		}
		
		
		if(!$item)
			$item = $this->model->createNewObject();

		$item->setValues($vals);			
		$item->compressBody();
		$item->save();
		
		$this->downloadAttachments($item);
		


		$this->doParse($item);	
		
	}
	
	function doDownloadMail()
	{
		$mail = $this->imap();
		
		if(isset($_GET['mailid'])){
			$uid=$_GET['mailid'];
		}else{
			$item = $this->getDataObjectById();
			$uid=$item->mailid;
		}		
		$mailid=$mail->getIdByUid($uid);
		
		
		if(!$mailid)
		{
			$this->setError("Probably removed");
			return false;
		}
		
		
		$message = (object)['mailid'=>$mailid];
		
		
		
		$mail->getMessage($message, true, true);
		
		$message->ruleid = $item->carrier;
		
		//d::ldump($message->structure);
		
		$this->messageProcess($message, $mail);
		
		d::dumpas($message);
		
		exit;
	}
	
	
	function doSearchMail()
	{
		//set_time_limit(60);
		
		$mail = $this->imap();
		
		
		
		$scanrules=['search'=>['subject'=>$_GET['query']]];
		
		$options = ['force'=>1];
		
		if(isset($_GET['start']))
			$options['start'] = $_GET['start'];

		if(isset($_GET['limit']))
			$options['limit'] = $_GET['limit'];
		
		if(isset($_GET['single']))
			$options['single'] = $_GET['single'];
		
		if(isset($_GET['withcontents']))
			$options['withcontents'] = $_GET['withcontents'];
		
		
		$data = $mail->getMessages($scanrules, $options);
		
		d::dumpas($data);
		
		if($data){
			echo("Found: $data->subject, body: ".$data->body.'attachments:'.count($data->attachments));
		}
		
						
	}
	
	function search($expr, &$data)
	{
		if (preg_match($expr, $data, $m))
			return $m[1];
	}	
	
	

	
	function downloadAttachments($item)
	{		
		
		//d::dumpas($item);
		
		$body = $item->getBody();
		
		//d::dumpas($item->attach_list);
		$rules = $this->getRules();
		$rule = $rules[$item->ruleid];
		
		if(!isset($rule['attachmentstore']))
			return false;
		
			
		
		foreach($item->attach_list as $attach){
			if(preg_match($rule['attachmentstore'], $attach->filename)){
				$filecontents = $this->downloadAttachment($item, $attach->filename);
				$this->storeAttachment($item, $filecontents, $attach->filename, ['title_lt'=>$attach->filename]);
			}
		}
		
	}
	
	
	
	
	function storeAttachment($mailitm, $file, $filename, $opts=[]) 
	{
		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		
		$tempfn = GW::s('DIR/TEMP').'attachment'.time().rand(0,100000).'.'.$extension;
		file_put_contents($tempfn, $file);
		
			
		$fileinfo = [
		    'new_file' => $tempfn,
		    'size' => filesize($tempfn),
		    'original_filename' => $filename,
		];
				
		$values['owner_type'] = $mailitm->ownerkey;
		$values['owner_id']= $mailitm->id;
		$values['field'] = 'attachments';
		$values['checksum'] = md5_file($tempfn);
				
		
		if(GW_Attachment::singleton()->count(GW_DB::buidConditions($values)))
		{
			//file already attached
			goto sFinish111;
		}
		
		list($type,$subtype) = explode('/', Mime_Type_Helper::getByFilename($tempfn));
		
		$values['content_cat'] = $type == 'image' && in_array($subtype, ['png','jpeg','gif']) ? 'image':'file';
		$values['content_type'] = $subtype;		

		$item = GW_Attachment::singleton()->createNewObject($values);
		
		if($opts['title_lt'] ?? false)
			$item->setTitle($opts['title_lt'], 'lt');
		
		
		$item->set($values['content_cat'], $fileinfo);
		
		//d::dumpas(file_get_contents($tempfn));
		//$item->set('extra/parse', $parse);
		
		
		$item->validate();
		$item->insert();
		
		//GW_File_Helper::output($tempfn);
		//d::Dumpas($file);
		
		sFinish111:
		
		
	}

	
	
	function downloadAttachment($item, $filename)
	{
		$mail = $this->imap();
		
		$uid=$item->mailid;
				
		$mailid=$mail->getIdByUid($uid);
		
		
		if(!$mailid)
		{
			$this->setError("Cand download attachment, message probably removed");
			return false;
		}
		
		foreach($item->attach_list as $partid => $attach)
		{
			
			if($attach->filename == $filename){
				
				//d::ldump('kazkas negerai su partid');
								
				return $mail->getPart($mailid, $partid, $attach->encoding);
			}
		}	
	}
	
	public function doGetAttachment() {
		$item = $this->getDataObjectById();
		$contents = $this->downloadAttachment($item, $_GET['file']);
		header('Content-Type: '.Mime_Type_Helper::getByFilename($_GET['file']));
		echo $contents;
		exit;
	}
	
	
	
	
	function doShowMail()
	{
		$item = $this->getDataObjectById();
		

		$body = $item->decompressBody();		
		
		$body = $this->tidy($body);
		
		die($body);
	}
	
	
	function doParseAll()
	{
		$list = $this->model->findAll();
		
		foreach($list as $item)
			$this->doParse($item);
		
	}
	
	function doParse($item=false)
	{
		$sys_call = $item ? true: false;
		
		
		if(!$item)
			$item = $this->getDataObjectById();
		
		


		$debug = isset($_GET['debug']);
		
		
		$body = $item->getBody();
		
		//jei senam irasui
		if($item->secondsPassedAfterCreate(60))
			$item->fireEvent('BEFORE_CHANGES');

		$ruleid = $item->ruleid;
		
		$item->save();
		
		if(!$ruleid){
			$this->setError("RuleID $method not avail");
			goto sFinish;
		}
		
		
		
		$method = "process$ruleid";
		if(method_exists($this, $method)){
			$this->$method($item);
		}else{
			$this->setError("RuleID $method not avail");
		}
		

			
		
		$item->save();
		
		/*
		if($item->changed_fields)
		{
			//$this->setMessage("Atnaujinta");
			
			
		}else{
			//$this->setMessage("Atnaujinimų nerasta");
		}*/
		
		
		sFinish:
		
		if(isset($_GET['debug']))
			return false;
		
		if(!$sys_call)
			$this->jump();		
	}	
	
	
	
	function getListConfig() 
	{
		$cfg = parent::getListConfig();
				
		
		unset($cfg['fields']['body']);

		

		foreach(['subject','headers','insert_time','update_time'] as $field)
			$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		
		$cfg['fields']['changetrack'] = 'L';	
						
		return $cfg;
		
	
	}
	
	

	
	function __eventAfterList($list)
	{

	}	

}




