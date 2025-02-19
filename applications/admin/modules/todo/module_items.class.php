<?php

class Module_Items extends GW_Common_Module_Tree_Data
{

	public $order_enabled_fields = [
		'id', 'title', 'priority', 'state', 'description', 'time_have', 'insert_time', 'update_time'
	];

	function init()
	{
		parent::init();

		$this->options['project_id'] = GW_Todo_Project::singleton()->getOptions();
		$this->options['project'] = GW::getInstance('gw_todo_project')->findAll(null, ['key_field' => 'id']);	
		
		$this->options['users'] = GW_User::singleton()->getOptions(true, 'is_admin=1');
				
	}

	function __eventBeforeListParams(&$params)
	{
		$params['conditions'] = 'type<2 ';

		//rodyti paskutini komentara
		$params['select'] = '*, (SELECT LEFT(description, 100) FROM gw_todo AS aaa WHERE aaa.parent_id=a.id ORDER BY `id` DESC LIMIT 1) AS last_comment, insert_time AS week';
	}

	function __eventAfterForm($item)
	{
		//d::dumpas($item->file1);
		
		//allow select only active projects
		//leaving old option
		$tmp = GW_Todo_Project::singleton()->getOptions('active=1');
		
		if(!isset($tmp[$item->project_id]) && isset($this->options['project_id'][$item->project_id]))
			$tmp[$item->project_id]=$this->options['project_id'][$item->project_id];
			
			
		$this->options['project_id'] = $tmp;
	}
	
	function __eventAfterList($list)
	{
		//to get first item
		foreach($list as $item)
			break;

		if(isset($item))
			if($item->extensions['attachments'])
				$item->extensions['attachments']->prepareList($list);
	}

	function doSwitchState()
	{

		$item = $this->getDataObjectById();
		$item->state = $_GET['state'];
		$item->user_exec = $this->app->user->id;

		$item->update(Array('state', 'user_exec'));

		$this->setPlainMessage(sprintf(GW::l('/m/ITEM_STATUS_CHANGED'), $item->id));

		$this->jump();
	}

	function overrideFilterComments($value)
	{
		$value = GW_DB::escape($value);
		$cond = " (SELECT count(*) FROM `gw_todo` AS aab WHERE aab.parent_id=a.id AND description LIKE '%$value%')>0 ";


		return $cond;
	}
	
	function overrideFilterWeek($value, $comparetype)
	{
		//$value = (int)$value;
		
		return $this->buildCond('WEEK(`insert_time`)', $comparetype, (int)$value, false, false);
	}

	
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['last_comment'] = 'Lo';
		$cfg['fields']['comments'] = 'f';
		$cfg['fields']['attachments'] = 'Lo';
		$cfg['fields']['info'] = 'L';
		$cfg['fields']['week'] = 'lf';

		
		
		
		$cfg['filters']['project_id'] = ['type'=>'multiselect','options'=>$this->options['project_id']];
		
		$eu = $this->model->getDistinctVals('user_exec');	
		$cu = $this->model->getDistinctVals('user_create');
		

		if($eu){
			$exec_users = GW_user::singleton()->getOptions(true,  GW_DB::inCondition("id", $eu));
			
			$cfg['filters']['user_exec'] = ['type'=>'multiselect','options'=> $exec_users];
		}
		if($cu){
			$create_users= GW_user::singleton()->getOptions(true, GW_DB::inCondition("id", $cu));
			$cfg['filters']['user_create'] = ['type'=>'multiselect','options'=> $create_users];			
		}
	
		return $cfg;
	}
	
	

}



