<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw_paysera_service
 *
 * @author wdm
 */
class gw_paysera_service 
{
	public $error=false;
	public $redirect_url=false;
	
	function init()
	{
		$this->app->initDB();
		$this->app->loadConfig();
	}

	function process()
	{
		$cfg = new GW_Config('datasources__payments_paysera/');
		$cfg->preload('');
				
		try {
			if($_GET['action']=='cancel' && $_GET['redirect_url'])
			{
				header('Location: '.$_GET['redirect_url']);
				exit;
			}else{
				$response = WebToPay::checkResponse($_GET, array(
					    'projectid' => $cfg->paysera_project_id,
					    'sign_password' => $cfg->paysera_sign_password,
				));
			}
			
			if(GW_Paysera_Log::singleton()->find(['orderid=? AND `action`=? AND handler=?', $response['orderid'],$_GET['action'],$_GET['handler']]))
			{
				//GW_Message::singleton();
				//notify someone about intereestin thing
			}
			
			$logvals = array_intersect_key($response, GW_Paysera_Log::singleton()->getColumns());	
			$logvals['action'] = $_GET['action'];
			$logvals['handler'] = $_GET['handler'];

			$logvals['handler_state'] = $this->{'handler'.$_GET['handler']}($response, $_GET['action']);

			$log_entry=GW_Paysera_Log::singleton()->createNewObject($logvals);
			$log_entry->insert();
			
			
			if($this->redirect_url){
				header('Location: '.$this->redirect_url);
				exit;
			}
			
			if($this->error)
				die($this->error);
			
			echo 'OK';
		} catch (Exception $e) {
			echo get_class($e) . ': ' . $e->getMessage();
		}
		
	}
	
	
	function handlerOrders($data, $action)
	{
		$order = Nat_Orders::singleton()->find(['id=?', $data['orderid']]);
		

		if ($data['type'] !== 'macro') {
			$this->error="Only macro payment callbacks are accepted";
			return -6;
		}			
		
		if(!$order){
			$this->error = "Order not found";
			return -1;
		}
		
		if($action=='accept' || $action=='callback')
		{
			$order->pay_type = 1;
			$order->pay_status = 7;
			$order->pay_time = date('Y-m-d H:i:s');
			$order->status = 3; //apmoketas
		}else{
			$order->pay_status = 0;
		}
		
		if($data['test'] != '0')
			$order->pay_test =1;
		
		if(isset($_GET['redirect_url']))
			$this->redirect_url = $_GET['redirect_url'];
		
		$order->updateChanged();
		
		return 1;
	}

}
