<?php


class GW_ClassificatorsExt extends GW_i18n_Data_Object
{
	public $i18n_fields = ['title'=>1,'text'=>1];
	public $default_order = 'type ASC, priority ASC';
	public $order_limit_fields=['type'];	
	public $calculate_fields = [
		'user_title'=>1,
		'user_obj'=>1
	];	
	
	function findByGroup($groupKey)
	{
		$group = GW_Classificator_Types::singleton()->find(['`key` =?', $groupKey]);
		
		if($group)
			return $this->findAll(['`type`=?', $group->id],['key_field'=>'id']);
	}
	
	function getKeyTitleOptions($cond, $ln)
	{
		//$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['key','title_'.$ln], $cond);
	}
	
	function calculateField($name) {
		
		switch($name){
			case 'user_obj':
				return GW_Customer::singleton()->find($this->user_id);
			break;			
			case 'user_title':
				$obj = $this->user_obj;
				return $obj ? "{$obj->id}. {$obj->title}" : "{$this->user_id}. N/A";
			break;
		
		}
		
		parent::calculateField($name);
	}

}