<?php

//call me example: http://site.url.and.or.base.path/service/smsclient/accept


class GW_SMSClient_service extends GW_Common_Service
{

	
	function init()
	{
		$this->initAdminAutoload();
		parent::init();
		
		$this->apikey = GW_Config::singleton()->get('datasources__sms/api_key');
	}
	
	function checkAuth()
	{
		return md5($this->apikey) == $_GET['verify'];
	}
	
	

	

	function actAccept()
	{
		
		$event = $_GET['event'];
		
		switch($event){
			case 'status':
				$id = $_GET['id'];
				$outsms = GW_Outg_SMS::singleton()->find(['remote_id=?', $id]);
				
				if(!$outsms)
					return ['error'=>404];
				
				$outsms->remote_status = $_GET['status'];
				$outsms->updateChanged();
			break;
			case 'incoming':
				
				$insms = GW_Inco_SMS::singleton()->createNewObject();
				$insms->remote_id = $_GET['id'];
				$insms->remote_replyto = $_GET['replyto'];
				$insms->msg = $_GET['msg'];
				$insms->number = $_GET['number'];
				$insms->time = $_GET['time'] ?? null;
				$insms->insert();
				
				
				$user = GW_Customer::singleton()->find(['phone=?', $insms->number]);
				$outsms = GW_Outg_SMS::singleton()->find(['remote_id=?', $insms->remote_id]);
				
				$body = "Nuo: $insms->number<br>";
				$body .= "Gauta: $insms->time<br><hr>";
				$body .= "$insms->msg";
				
				if($user){
					$link = GW::s('SITE_URL')."admin/lt/customers/users/{$user->id}/form";
					$body .= "<hr>Kliento id <a href='$link'>{$user->id} {$user->title}</a>";
				}
				if($outsms){
					$body .= "<hr>Žinutė yra atsakymas į prieš tai siųsta sms: {$outsms->msg}";
				}				
				
				$opts = ['subject'=>'Gautas SMS atsakymas iš '.$insms->number, 'body'=>$body];
				GW_Mail_Helper::sendMailAdmin($opts);
				
				return ['insert_id'=>$insms->id];
			break;		
		}
	}
}
