<?php

class Module_Users extends GW_Common_Module 
{

	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
		
		
		$this->options['sms_pricing_plan']=GW::getInstance('GW_Pricing_Item')->getAllPricingPlans();
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
				
				$context->setValidators('update');
				
			break;
		}
		
		parent::eventHandler($event, $context);
	}

	function doAddCredit()
	{
		$item = $this->getDataObjectById();

		$add = (float)$_REQUEST['addcredit'];
		
		$old = $item->credit;
		
		$item->addFunds($add, "Papildymas");
		
		$new = $item->credit;
		/*
		if($item->phone)
			Sms_Outgoing::systemMessage($item->phone, "JÅ«sÅ³ sÄsk. papildyta: $add, viso dabar turite: $new -- sms.gw.lt");
		*/
		$this->app->setMessage("User <b>$item->username</b> credit changed from $old to $new");
		
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
