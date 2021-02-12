<?php


class Module_Usr extends GW_Common_Module
{	
	public $order_enabled_fields=['id','username','name'];
	
	function init()
	{
		$this->model = new GW_User();
		$this->group0 = new GW_Users_Group();
				
		
		
		$this->filters['removed'] = 0;
		
		$this->config = new GW_Config('gw_'.$this->module_path[0].'/');
		$this->initAdmin();
		
		if(!$this->rootadmin && !$this->isSuperAdmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
		
		$this->options['parent_user_id'] = GW::getInstance('GW_User')->getOptions(false);
		
		
		
		$this->__initGroupOptions();
		parent::init();
		
		
		$this->list_params['paging_enabled']=1;
	}
	
	function initAdmin()
	{
		$this->rootadmin = $this->app->user->isRoot();
		
		$this->isSuperAdmin = in_array($this->config->superadmin_group, $this->app->user->group_ids);
	}

	function __initGroupOptions()
	{
		$options = $this->group0->getAssoc(Array('id','title'));
		
		if(!$this->app->user->isRoot())
			unset($options[$this->group0->root_group_id]);
		
		$this->options['group_ids'] =$options;
	}
		

	function canBeAccessed($item, $opts=[])
	{	
		//parent::canBeAccessed($item);
		$item->load_if_not_loaded();
		
		$result = ($this->rootadmin) || $item->id==0 || ($item->parent_user_id == $this->app->user->id);
		
		if(!isset($opts['die']) || $result)
			return $result;

		$this->setError('/G/GENERAL/ACTION_RESTRICTED');
		
		
		$this->jump($this->app->page->path);
	}
	
	
	
	
	function __eventBeforeDelete($item)
	{
		if($item->get('id') == $this->app->user->get('id')){
			$this->setError($this->lang['ERR_DELETE_SELF']);
			$this->jump();
			exit;
		}
		
	}
	
	function __eventBeforeInvertActive($item)
	{
		if($item->get('id') == $this->app->user->get('id'))
			return $this->setError($this->lang['ERR_DEACTIVATE_SELF']);		
	}
	
	
	function doSwitchUser()
	{
		if(!$item = $this->getDataObjectById())
			return;
		
		$this->canBeAccessed($item);	
		
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
	
	function getEnabledFields()
	{
		$availfields = explode(',',$this->config->available_fields);
		$enabled =  array_flip(json_decode($this->config->fields_enabled));
		
		foreach($availfields as $field)
			$enabled[$field] = isset($enabled[$field]);

		
		return $enabled;
	}
	
	function getListConfig()
	{
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'username' => 'Lof',
			'name'=> 'Lof',
			'group_ids' => 'Lf',
			'online'=>'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',
			'parent_user_id'=>'lof',
			'name'=>'lof',
			'surname'=>'lof',
			'city'=>'lof',
			'gender'=>'lof',
			'email'=>'lof',
			'image'=>'lof',
			'login_count'=>'lof',
			'last_ip'=>'lof',
			'last_request_time'=>'lof',
			]
		);
	
		foreach($this->getEnabledFields() as $field => $enabled)
			if(!$enabled)
				unset($cfg['fields'][$field]);
		
		
		$cfg['filters']['group_ids'] = ['type'=>'multiselect','options'=>$this->options['group_ids']];
			
		return $cfg;
	}
	
	function overrideFilterGroup_Ids($value,$compare_type){
		
		$incond = GW_DB::inCondition("`lug`.`id1`", $value);
		$sql = "(SELECT count(*) FROM `gw_link_user_groups` AS lug WHERE `lug`.`id`=`a`.`id` AND $incond) > 0";
		
		return $sql;
	}	
	
	function viewIpLog()
	{
		$item = $this->getDataObjectById();
		
		$list = GW_User_Ip_Log::singleton()->findAll(['user_id=?', $item->id]);
		
		$this->tpl_vars['list'] = $list;
	}
	
	
	function getOptionsCfg()
	{
		$opts = [
		    //'title_func'=>[LTF_Participants::singleton(),'getTeamName'],
		    'search_fields'=>['name','surname','email','username']
		];	
		
		return $opts;	
	}	

}
