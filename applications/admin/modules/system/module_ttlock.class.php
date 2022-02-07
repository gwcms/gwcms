<?php


class Module_TTlock extends GW_Common_Module
{	
		
	function init()
	{
		$this->model = new gw_ttlock_codes();
		
		parent::init();
		
		//to log cron tasks
		$this->initLogger();
		$this->list_params['paging_enabled']=1;
	}

	
	function viewTESTs()
	{
	
		$test_actions = [];
		$test_views = [];
		
		$list = get_class_methods ($this);
		foreach($list as $method){

			if(stripos($method, 'doTest')===0)
				$test_actions[]=[$method, $this->$method];
			
			if(stripos($method, 'viewTest')===0)
				$test_views[]=[substr($method,4), $this->$method];			
		}
				
		$this->tpl_vars['test_actions']=$test_actions;
		$this->tpl_vars['test_views']=$test_views;
		
		

	}
	
	
	
	public $doTestList = ["info"=>"Get list of devices"];	
	
	function doTestList()
	{
		
		//$r = ttlock_api::singleton()->init()->getLockList();
		//d::ldump($r);
		
		//nera tokio
		$list = ttlock_api::singleton()->init()->listAllPasscode();
		d::ldump($r);
	}
	
	public $doTestListPasscodes = ["info"=>"List all pascodes"];	
	
	function doTestListPasscodes()
	{

		$list = ttlock_api::singleton()->init()->listAllPasscode();
		

		//print_r($list);
		
		
		echo GW_Data_to_Html_Table_Helper::doTable($list);
		
	}	
	
	public $doTestAddPassCode = ["info"=>"Set time limited passcode (123456) from now+1minute - 20min long"];	
	function doTestAddPassCode()
	{
		
		$form = ['fields'=>[
			'passcode'=>['type'=>'text','required'=>1]
		    ],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/ACTION_REQUESTS_ADDITIONAL_INPUT'))))
			return false;		
		
		$passcode = $answers['passcode'];
		

		$r = ttlock_api::singleton()->init()->addPasscode(false,$passcode,strtotime('+1 MINUTE'),strtotime("+20 MINUTE"));
		
		d::ldump([$r]);
	}
	
	
	
	public $doTestAddRandomPassCode = ["info"=>"Set time limited passcode (123456) from now+1minute - 20min long"];	
	
	function doTestAddRandomPassCode()
	{
		$passcode="123456";
		$r = ttlock_api::singleton()->init()->addPasscodeRandom(false,$passcode,6,strtotime('+1 MINUTE'),strtotime("+20 MINUTE"));
		
		d::ldump([$r,['code'=>$passcode]]);
	}
	
	public $doTestDeletePasscode = ["info"=>"Delete passcode"];	
	
	function doTestDeletePasscode()
	{
		$form = ['fields'=>[
			'passid'=>['type'=>'text','required'=>1]
		    ],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/g/ACTION_REQUESTS_ADDITIONAL_INPUT'))))
			return false;		
				
		
		
		ttlock_api::singleton()->init()->deletePasscode(false,$answers['passid']);
		//api.ttlock.com/v3/keyboardPwd/delete
		
		
		
	}
	
	
	//supported by doSeriesAct	
	function doRemoteDelete($item=false)
	{
		$seriesact = (bool)$item;
		if(!$item)
			$item = $this->getDataObjectById();
		
		//$item->remote_id = 0;
		//$item->updateChanged();		

		$resp = ttlock_api::singleton()->init()->deletePasscode(false,$item->remote_id);

		if(isset($resp->errcode) && $resp->errcode!=0){
			$this->setMessage($item->id.' - error - '.json_encode($resp));
		}else{
			$item->remote_id = 0;
			$item->updateChanged();			
		}
		
		if(!$this->sys_call && !$seriesact)
			$this->jump();		
	}
	
	
	function getOptionsCfg()
	{
		$opts = [
			'title_func'=>function($item){ 
				$start = date('Y-m-d H:i',strtotime($item->start));
				$end = date('Y-m-d H:i',strtotime($item->end));
				return $item->get("code")." ($start - $end)";  
			
			},
			'search_fields'=>['code']			
		];	
		

		
		return $opts;	
	}		
	
	//supported by doSeriesAct	
	function doRemoteCreate($item=false)
	{
		$seriesact = (bool)$item;
		if(!$item)
			$item = $this->getDataObjectById();
		
		$passcode = $item->code;
		
		$resp = ttlock_api::singleton()->init()->addPasscode(false,$passcode,strtotime($item->start),strtotime($item->end));
		

		
		if(isset($resp->keyboardPwdId) && $resp->keyboardPwdId){
			$item->remote_id = $resp->keyboardPwdId;
			$item->updateChanged();
			
		}else{
			$this->setMessage($item->id.' - error - '.json_encode($resp));
		}
		
		if(!$this->sys_call && !$seriesact)
			$this->jump();		
	}
	
	public $doTestUnlock = ['info'=>'Atrakinti spyna'];
	
	function doTestUnlock()
	{
	
		$r = ttlock_api::singleton()->init()->unlock(false);
		d::ldump($r);
		
	}

	public $doTestlock = ['info'=>'Užrakinti spyna'];
	
	function doTestlock()
	{
	
		$r = ttlock_api::singleton()->init()->lock(false);
		d::ldump($r);
		
	}	
	
	
	function doRemoveRemoteOld()
	{
		$cnt = 0;
		while(true){
			$cnt++;
			$item = $this->model->find(['`end` < ? AND remote_id>0', date('Y-m-d H:i')]);
			

			if($item){

				$this->setMessage("id{$item->id} #{$item->code} / {$item->remote_id} [{$item->start} - {$item->end}] - item processing");
				$this->doRemoteDelete($item);
				
				if($item->remote_id){
					$this->setError("id{$item->id} / {$item->remote_id} - item failed");
				}else{
					$this->setMessage("id{$item->id} / {$item->remote_id} - item done");
				}
				sleep(1);
			}else{
				break;
			}
			if($cnt>20)
				break;
		
		}
		
		if(isset($_GET['cron']))
			exit;
		
		$this->jump();
	}
	
	
	function doRemoveRemoteOld2()
	{
		$list = ttlock_api::singleton()->init()->listAllPasscode();		
		
		$curdate = date('Y-m-d H:i');
		
		$cnt =0;
		
		foreach($list as $item){
			if($item->endDate < $curdate && $item->keyboardPwdName == 'voro-api')
			{
				$cnt++;
				$resp = ttlock_api::singleton()->init()->deletePasscode(false,$item->keyboardPwdId);
				$this->setMessage(json_encode(['delete'=>$item,'response'=>$resp]));

				
				sleep(1);
				
				if($cnt>20)
					break;
			}
		}
		
	}
	
	
}
