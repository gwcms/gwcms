<?php


class Module_Usr extends GW_Common_Module
{	
	public $order_enabled_fields=['id','username','name'];
	
	function init()
	{
		$this->model = new GW_User();
		$this->group0 = new GW_Users_Group();
				
		$this->rootadmin = $this->app->user->isRoot();
		
		$this->filters['removed'] = 0;
		
		if(!$this->rootadmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
		
		$this->options['parent_user_id'] = GW::getInstance('GW_User')->getOptions(false);
		
		
		
		$this->__initGroupOptions();
		parent::init();
		
		$this->list_params['paging_enabled']=1;
	}

	function __initGroupOptions()
	{
		$options = $this->group0->getAssoc(Array('id','title'));
		
		if(!$this->app->user->isRoot())
			unset($options[$this->group0->root_group_id]);
		
		$this->options['group_ids'] =$options;
	}
		

	function canBeAccessed($item, $die=true, $load = true)
	{	
		$item->load_if_not_loaded();
		
		$result = ($this->rootadmin) || ($item->parent_user_id == $this->app->user->id);
		
		
		
		if(!$die || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}
	
	
	
	function doDelete()
	{	
		if(!$item = $this->getDataObjectById())
			return;
			
		$this->canBeAccessed($item, true);	
			
		if($item->get('id') == $this->app->user->get('id'))
			return $this->setError($this->lang['ERR_DELETE_SELF']);	
			

		$item->delete();
		$this->setPlainMessage($this->app->lang['ITEM_REMOVE_SUCCESS']);
		
		$this->jump();
	}
	
	function doInvertActive()
	{
		if(!$item = $this->getDataObjectById())
			return;

		if($item->get('id') == $this->app->user->get('id'))
			return $this->setError($this->lang['ERR_DEACTIVATE_SELF']);
			
		parent::doInvertActive();
	}
	
	
	function doSwitchUser()
	{
		if(!$item = $this->getDataObjectById())
			return;
		
		$this->canBeAccessed($item, true);	
		
		// jei ne root tai neleisti pasikeisti i root
		
		// jei admin grupej iseiti
		
		
		$this->app->auth->switchUser($item->id);
		
		$this->jump();
	}
	

	
	
	function viewMessage()
	{
		//GW_Message//
		$user = $this->getDataObjectById();
				
		$this->tpl_vars['user']=$user;
		
	}
	
	function doMessage()
	{
	
		$vals = $_REQUEST['item'];
		
		GW::getInstance('GW_Message')->msg($vals['user_id'], $vals['subject'], $vals['message'], $this->app->user->id);
		
		$this->setPlainMessage($this->lang['SENT']);
		
		$this->jumpAfterSave();
	}
	
	function getFiltersConfig()
	{
		return [
			'id' => 1,
			'username' => 1,
			'name' => 0,
			'insert_time' => 1,
		];
	}


	function __eventAfterList(&$list)
	{
		//attach parent user titles
		if($this->rootadmin){
			#attach counts
			$parentusers=[];

			foreach($list as $item)
				if($item->parent_user_id)
					$parentusers[$item->parent_user_id]=1;
			
			foreach($parentusers as $key => $x)
				$parentusers[$key] = GW_User::singleton()->find(['id=?', $key]);
			
			
			foreach($list as $item)
				if($item->parent_user_id)
					$item->parent_user_title = isset($parentusers[$item->parent_user_id]) ? $parentusers[$item->parent_user_id]->title : '';
				
		}
	}

	
	function overrideFilterOnline($value, $comparetype)
	{
		//$value = (int)$value;
		$before10mins= date('Y-m-d H:i:s', strtotime('-10 minute'));
		
		if($value || $value==='0')
			return "last_request_time ".($value  && $value!='0' ? '>' : '<')." '$before10mins'";
	}
	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'username' => 'Lof',
			'name'=> 'Lof',
			'group_ids' => 'L',
			'online'=>'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',
			'parent_user_id'=>'lof',
			'name'=>'lof',
			'surname'=>'lof',
			'birth_date'=>'lof',
			'gender'=>'lof',
			'person_id'=>'lof',
			'username'=>'lof',
			'person_document_id'=>'lof',
			'phone'=>'lof',
			'email'=>'lof',
			'workplace'=>'lof',
			'workplace_occupation'=>'lof',
			'bank_account'=>'lof',
			'actual_addr_street'=>'lof',
			'actual_addr_city'=>'lof',
			'legal_addr_street'=>'lof',
			'legal_addr_city'=>'lof',
			'name'=>'lof',
			'name'=>'lof',
			'agreement_nr'=>'lof',
			'salary'=>'lof',
			'campaign_id'=>'lof',
			'is_blocked'=>'lof',
			'lang'=>'lof',
			'insert_time'=>'lof',
			'active'=>'lof',
			'deny_till'=>'lof',
			'newsletter'=>'lof',
			'bank_confirm_date'=>'lof',
			'bank_account_confirmed'=>'lof',
			'registration_ip'=>'lof',
			'workplace_phone'=>'lof',
			'contact_person'=>'lof',
			'contact_person_phone'=>'lof',
			'last_user_agent'=>'lof',
			'referer'=>'lof',
			'labels'=>'lof',
			'import_broker'=>'lof',
			'import_id'=>'lof',
			'import_public_id'=>'lof'	
			]
		);
		
		//$cfg['filters']['project_id'] = ['type'=>'select','options'=>$this->options['project_id']];
			
			
		return $cfg;
	}
	
	function viewIpLog()
	{
		$item = $this->getDataObjectById();
		
		$list = GW_User_Ip_Log::singleton()->findAll(['user_id=?', $item->id]);
		
		$this->tpl_vars['list'] = $list;
	}
	
	
	function doRemoveTestImportData()
	{
		$list = $this->model->findAll('lang="ge"');
		
		foreach($list as $item)
			$item->forceDelete();
		
		
		$this->setMessage('Removed items: '.count($list));
		
		$this->jump();
	}
	
	function __import()
	{
		$offset = (int)GW_Config::singleton()->get('ASC/USERS/IMPORT_LAST_OFFSET');
		$take = 3000;
		
		
		$rows = GW::db()->fetch_rows("SELECT * FROM acs_users_old LIMIT $offset, $take");
		
		
		
		
		
		
		foreach($rows as $row)
		{
			
			$addrows=GW::db()->fetch_assoc('SELECT fieldName,fieldValue FROM acs_users_old_data WHERE parentId='.(int)$row['id']);
			
			$itm = $this->model->createNewObject();
			$itm->setValues([
				'id'=>$row['id']+10000,
				'name'=>$row['name'],
				'surname'=>$row['surname'],
				'birth_date'=>$row['birthDate'],
				'gender'=>$row['gender'],
				'person_id'=>$row['persId'],
				'username'=>$row['persId'],
				'person_document_id'=>$row['persId2'],
				'phone'=>$row['phone'],
				'email'=>$row['email'],
				'workplace'=>$row['workplace'],
				'workplace_occupation'=>$row['job'],
				'bank_account'=>$row['bankAccount'],
				'actual_addr_street'=>$row['actualAddrStreet'],
				'actual_addr_city'=>$row['actualAddrCity'],
				'legal_addr_street'=>$row['legalAddrStreet'],
				'legal_addr_city'=>$row['legalAddrCity'],
				'name'=>$row['name'],
				'name'=>$row['name'],
				'agreement_nr'=>$row['agreementNumber'],
				'salary'=>$row['salary'],
				'campaign_id'=>$row['campaignId'],
				'is_blocked'=>$row['isBlocked'],
				'lang'=>$row['lang'],
				'insert_time'=>$row['registrationDate'],
				'active'=>$row['isBlocked'] ? '0':'1',
				'deny_till'=>$row['denyTillDate'],
				'newsletter'=>$row['newsletter'],
				'bank_confirm_date'=>$row['bankConfirmDate'],
				'bank_account_confirmed'=>$row['bankAccountConfirmed'],
				'registration_ip'=>$addrows['registrationIp'],
				'workplace_phone'=>@$addrows['workplacePhone'],
				'contact_person'=>$addrows['contactPerson'],
				'contact_person_phone'=>$addrows['contactPersonPhone'],
				'last_user_agent'=>$addrows['userAgent'],
				'referer'=>@$addrows['referer'],
				'labels'=>$row['labels'],
				'import_broker'=>$row['brokerId'],
				'import_id'=>$row['id'],
				'import_public_id'=>$row['publicId'],
				
				//'sms_confirmed'=>$row['smsConfirmed'],
				
				
			]
			);
			
			$itm->insert();
			
			//print_r($addrows);
		}
		
		$this->setMessage("Imported items: ".count($rows));
		
		GW_Config::singleton()->set('ASC/USERS/IMPORT_LAST_OFFSET',$offset+$take);	
		
		return count($rows);
	}
	
	function doTestImport()
	{
		while(1){
			$imp_cnt = $this->__import();
			
			if(!$imp_cnt)
				break;
		}
		
		
		$this->jump();
	}
	
	
	function doTestTranslate()
	{
		$list = $this->model->findAll('lang="ge"');
		
		
		$transfields=['name','surname','workplace','workplace_occupation','legal_addr_street','legal_addr_city','contact_person','actual_addr_street','actual_addr_city'];
		
			
			
		include GW::s('DIR/ROOT_DIR')."vendor/autoload.php";



		$tr = new Stichoza\GoogleTranslate\TranslateClient('ge', 'en');

		
		foreach($list as $item)
		{
			foreach($transfields as $idx => $field)
				$transvals[$idx] = $item->get($field);
			
			$translated = $tr->translate($transvals);
			
			foreach($transfields as $idx =>$field)
				if(isset($translated[$idx]) && $translated[$idx])
					$item->set($field, $translated[$idx]);
			
			$item->updateChanged();
		}
		
		$this->setMessage("Translated items: ".count($rows));
		
		$this->jump();		
	}
	
		
}

