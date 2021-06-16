<?php


class GW_Classificators extends GW_i18n_Data_Object
{
	public $i18n_fields = ['title'=>1];
	
	function findByGroup($groupKey)
	{
		$group = GW_Classificator_Types::singleton()->find(['`key` =?', $groupKey]);
		
		if($group)
			return $this->findAll(['`type`=?', $group->id],['key_field'=>'id']);
	}

}