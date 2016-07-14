<?php

class Module_Users extends GW_Common_Module 
{

	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
		$this->cfg = new GW_Config($this->module_path[0].'/');
		
		
		$this->rootadmin = $this->app->user->isRoot();
		
		if(!$this->rootadmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
		
		$this->options['parent_user_id'] = GW::getInstance('GW_User')->getOptions(false);		
		
		$this->options['sms_pricing_plan']=GW::getInstance('GW_Pricing_Item')->getAllPricingPlans();
		
		$this->list_params['paging_enabled']=1;
	}
	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function eventHandler($event, &$context) 
	{
		switch($event)
		{
			case "BEFORE_SAVE_0":
				
				$item = $context;
				
				if($item->id){
					$item->setValidators('update');
				}else{
					$item->setValidators('insert');
					$item->group_ids = [$this->cfg->customer_group];
					
					$item->parent_user_id = $this->app->user->id;
				}
				
				
				
			break;
		}
		
		parent::eventHandler($event, $context);
	}

	function doAddCredit()
	{
		$item = $this->getDataObjectById();
		
		$before_funds = $item->sms_funds;

		$add = (float)$_REQUEST['addcredit'];
				
		$r=$item->addFunds($add, "Papildymas");
		
		extract($r);
		/*
		if($item->phone)
			Sms_Outgoing::systemMessage($item->phone, "JÅ«sÅ³ sÄsk. papildyta: $add, viso dabar turite: $new -- sms.gw.lt");
		*/
		$this->app->setMessage("User <b>$item->username</b> credit changed from $old to $new");
		
		$after_funds = $item->sms_funds;
		$subject = "Jūsų sąskaita papildyta ".$add." Eur";
		$message = "Prieš papildymą buvo: $before_funds Eur. <br />Dabar yra: $after_funds Eur";
		
		GW_Message::singleton()->msg($item->id, $subject, $message, $this->app->user->id, 0, false);
		
		$this->jump();
	}
	
	function viewBalanceLog()
	{
		$item = $this->getDataObjectById();
		$list = gw::getInstance('GW_Balance_Log_Item')->findAll(['user_id=?', $item->id],['order'=>'id DESC']);
		
		$this->smarty->assign('list', $list);
		
		//return ['list'=>$list];
	}
}

?>
