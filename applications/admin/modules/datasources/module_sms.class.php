<?php


class Module_Sms extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->config = new GW_Config($this->module_path[0].'__'.$this->module_path[1].'/');
		$this->config->preload('');
		
		$this->initLogger();
		
		$this->app->carry_params['number']=1;
		$this->app->carry_params['clean']=1;
		
		if(isset($_GET['number'])){
			$this->filters['number'] = $_GET['number'];
			$this->list_params['paging_enabled']=false;	
		}
		
		//d::dumpas($this->list_params);
	}
	
	
	
	function gwSendSms($to, $msg, $opts=[], &$err=false, &$extra=[])
	{
		
		$addarg="";
		if(isset($opts['add_balance']))
			$addarg = "&add_balance=1";
		
		$uid = $this->config->gwlt_user_id;
		$api_key = $this->config->gwlt_api_key;
		$host = $this->config->gwlt_host;
		
		if($this->config->route)
			$addarg.="&route_id={$this->config->gwlt_route}";
			
			
		$context = stream_context_create(array('http' => array('ignore_errors' => true)));
		$resp = file_Get_contents($url = "http://{$host}/service/mis/public/sendsms?uid={$uid}&api_key={$api_key}&to=$to&msg=".urlencode($msg).$addarg, false, $context);
		
//		/d::dumpas([$http_response_header, $url, $resp]);
		
		$resp = json_decode($resp,true);
		
		
		$extra = $resp;
		
		if(isset($resp['message']['status']) && $resp['message']['status']==4)
		{
			return true;
		}else{
			$err = $resp;
			return false;
		}
	}


	function doT2SendSms()
	{
		// Usage Example
	
		//d::dumpas([$this->config->gateway, $this->config->tele2sms_apikey, $this->config->tele2sms_sender]);
		
		$smsApi = new Tele2SMSApi($this->config->tele2_apikey);

		// Send SMS
		$senderName = $this->config->tele2_sender;
		$recipients = ["37060089089"];
		$message = "Bandome siusti sms 9000";
		$response = $smsApi->sendSMS("asdfasdfa", $recipients, $message);
		
		
		

		// Check SMS Status
		$requestId = $response['requestId']; // UUID from previous response

		
		d::dumpas([$response,$requestId, $status]);		
	}
	
	
	
	/*
		1 - Message ready to be delivered.
		2 - Message being processed.
		3 - Message rejected.
		4 - Recipient (phone number) rejected.
		5 - Message expired and was not delivered.
		6 - Message delivered successfully.
		7 - Error while processing the message.
	 */
	function doT2CheckStatus(){
		$smsApi = new Tele2SMSApi($this->config->tele2sms_apikey);
		
		
		$form = ['fields'=>[ 'requestid'=>['required'=>1] ],'cols'=>1];
		
		if(!($answers=$this->prompt($form, "Requestid")))
			return false;	
		//po 5s grazina state=1, maziau nei 5 sek nieko negrazina
		$status = $smsApi->getSMSStatus($answers['requestid']);
		d::dumpas($status);
	}
	
	
	
	function __eventAfterSave($item)
	{	
		if($_POST['submit_type']==7)
		{
			$this->doSend($item);
		}
	}


	function doSend($item=false)
	{
		if(!$item){
			$item = $this->getDataObjectById();
			$sys_call = false;
		}else{
			$sys_call = true;
		}
		
		
		
		$item->gw = $this->config->gateway;
		
		if($this->config->gateway=='tele2'){
			// Send SMS
			
			$smsApi = new Tele2SMSApi($this->config->tele2_apikey);
			$senderName = $this->config->tele2_sender;
			$recipients = [$item->number];
			$message = $item->msg;
			$response = $smsApi->sendSMS($senderName, $recipients, $message);

			//ALTER TABLE `gw_outg_sms` CHANGE `remote_id` `remote_id` VARCHAR(40) NOT NULL;
			$item->remote_id = $response['requestId'];
			
			if($item->remote_id){
				$item->status = 7;
			}else{
				$item->status = 6;
				$item->err = json_encode($response);
			}
		}else{
		
			if($this->gwSendSms($item->number, $item->msg, ['add_balance'=>1], $err, $extra)){
				$item->status = 7;
				$item->err = "";
				//$item->remote_id = $extra['message']['remote_id'];
				$item->remote_id = $extra['message']['id'];

				$this->setmessage("Message sent!");
				$stat = "SENT";
			}else{
				$item->status = 6;
				$item->err = json_encode($err);
				$this->setError("Message failed!");
				$stat = "FAILED (".json_encode($err).")";
			}
			
			$item->enc = $extra['message']['encoding'];
			$item->parts = $extra['message']['parts_count'];
			$item->weight = $extra['message']['credit'];
			//$this->setError(json_encode($extra['message']));	
			
			$this->config->last_response = json_encode($extra);
			$this->config->last_send_info = date('Y-m-d H:i:s').' '.$stat.' [balance: '.$extra['balance'].']';
		}
		
		
		
		

		$item->updateChanged();
		$this->notifyRowUpdated($item->id, false);
	}
	
	function doRemoteStatusCheck()
	{
		$checked = 0;
		
		if($this->config->gateway=='tele2'){
			$list  = $this->model->findAll(['gw="tele2" AND remote_status<1 AND remote_id!=""'],['limit'=>10]);
			$smsApi = new Tele2SMSApi($this->config->tele2_apikey);
			
			foreach($list as $sms){
				
				$status = $smsApi->getSMSStatus($sms->remote_id);
				
				//d::dumpas([$sms, $status]);
				
				if( ($status['messages'][0]['receiver']??false) == $sms->number){
					
					$sms->remote_status = $status['messages'][0]['state'];
					
					if($status['messages'][0]['error'] ?? false)
						$sms->err = $status['messages'][0]['error'];
				}else{
					$this->setError("Expected number: $sms->number found:".$status['messages'][0]['receiver']);
					$sms->remote_status = -1;
				}
				
				$sms->updateChanged();
			}
			
			$checked = count($list);
		}
		
		$this->setMessage("Checked: $checked");
	}
	
	
	
	function doRetrySend()
	{
		
		//status - 6  fail
		//status - 0 in queue
		$curtime = date('Y-m-d H:i:s');
		$list = GW_Outg_SMS::singleton()->findAll(['(send_time IS NULL OR send_time < ?) AND retry < 2 AND (status=6 OR status=0) AND insert_time + INTERVAL 3 DAY > NOW()', $curtime]);
		//$picksql = GW::db()->last_query;
		
		$found = count($list);
		$succ = 0;
		
		foreach($list as $item){
			
			$item->saveValues(['retry'=>$item->retry+1]);
			$this->doSend($item);
			if($item->status==7)
				$succ++;
		}
		
		$this->setMessage($stat = "Found $found, resend success: $succ");
		
		
		//if($this->app->user->isRoot())
		//	d::ldump($picksql);
		
		$this->config->last_retry_status = $stat.', '.date('Y-m-d H:i:s');
	}
	
	//alias to doRetrySend
	function doSendQueue()
	{
		$this->doRetrySend();
		$this->doRemoteStatusCheck();
		
		if( ($_GET['cron'] ?? false) )
			die('done');
	}
	
	function viewConfig()
	{
		return ['item'=>$this->config];
	}	
	
	function doSaveConfig()
	{
		$vals = $_REQUEST['item'];
		
		$this->config->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
				
		
		$this->jump();		
	}
	
	
	function doInsertNew()
	{
		$item = GW_Outg_SMS::singleton()->createNewObject();
		$item->set('number', $_GET['number']);
		$item->set('msg', $_GET['msg']);
		
		if($_GET['send_time'] ?? false)
			$item->send_time = $_GET['send_time'];
		
		$item->insert();
		
		
		if($item->send_time == false || $item->send_time == '0000-00-00 00:00:00'){
			Navigator::backgroundRequest('admin/'.$this->app->ln.'/datasources/sms?act=doSend&id='.$item->id);	
		}
		
		if($item->id){
			$this->setMessage("Sms message created",[
				'title'=> $item->number.': '.GW_String_Helper::truncate($item->msg, 50), 
				'float'=>1, //for user interface if this response message will be used to directly passing to user
				'action'=>'result', //for innerRequest
				"item_id"=>$item->id
			]);
			
			/*
			$this->app->addPacket([
			    'action'=>'results', 
			    'result'=>'insertsuccess',
			]);
			 * 
			 * 
			 */			
		}
		
		$this->jump();
	}
	
	function prepareListConfig($item = false)
	{
		$x = parent::prepareListConfig($item);
		
		if(isset($this->filters['number']))
			$this->tpl_vars['dl_filters']=[];
		
		return $x;
	}
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
}
