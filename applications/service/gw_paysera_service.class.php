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
		$cfg = new GW_Config('payments__payments_paysera/');
		$cfg->preload('');
				
		try {
			if($_GET['action']=='cancel' && $_GET['redirect_url'])
			{
				header('Location: '.$_GET['redirect_url']);
				exit;
			}else{
				try {
					$response = WebToPay::checkResponse($_GET, array(
					    'projectid' => $cfg->paysera_project_id,
					    'sign_password' => $cfg->paysera_sign_password,
					    'log' => GW::s('DIR/LOGS') . 'paysera.log'
					));

				} catch (Exception $e) {
				    
					if($_GET['action']=='callback'){
						$data = ['uri'=>$_SERVER['REQUEST_URI'], 'error'=>$e->getMessage(), '_POST'=>$_POST ?? []];
						$data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
						mail('vidmantas.work@gmail.com', 'paysera err', $data, "From: info@ltf.lt\r\n");
					}
					header('Location: '.$_GET['redirect_url']);
					exit;
				}
				
			}
			
			if(GW_Paysera_Log::singleton()->find(['orderid=? AND `action`=? AND handler=?', $response['orderid'],$_GET['action'],$_GET['handler']]))
			{
				//GW_Message::singleton();
				//notify someone about intereestin thing
			}
			
			$logvals = array_intersect_key($response, GW_Paysera_Log::singleton()->getColumns());	
			$logvals['action'] = $_GET['action'];
			$logvals['handler'] = $_GET['handler'];
			$log_entry=GW_Paysera_Log::singleton()->createNewObject($logvals);
			$log_entry->insert();			

			$log_entry->handler_state = $this->{'handler'.$_GET['handler']}($response, $_GET['action'], $log_entry);
			$log_entry->update();

			
			
			if($this->redirect_url){
				header('Location: '.$this->redirect_url);
				exit;
			}
			
			if($this->error)
				die($this->error);
			
			echo 'OK';
			exit;
			
		} catch (Exception $e) {
			echo get_class($e) . ': ' . $e->getMessage();
		}
		
	}
	
	

	
	function handlerOrders($data, $action, $log_entry)
	{
		$p = explode('-',$data['orderid']);
		$id = $p[1];
		

		if ($data['type'] !== 'macro') {
			$this->error="Only macro payment callbacks are accepted";
			return -6;
		}			
		
		
		//
		if($action=='callback')
		{	
			$args = [
			    'id'=>$id,
			    'rcv_amount'=>$log_entry->amount / 100,
			    'log_entry_id'=>$log_entry->id,
			    'pay_type'=>'paysera'
			];
			
			if($data['test'] != '0'){
				$args['paytest'] = 1;
			}
			
			
			$url=Navigator::backgroundRequest('admin/lt/payments/ordergroups?act=doMarkAsPaydSystem&sys_call=1&'. http_build_query($args));
			
			file_put_contents(GW::s('DIR/TEMP').'lastpayment_approve_link', $url);
		
		}else{
			//nothing
		}
				
		if(isset($_GET['redirect_url']))
			$this->redirect_url = $_GET['redirect_url'];
		
		
		return 1;
	}	
	
	

}
