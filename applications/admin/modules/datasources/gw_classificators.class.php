<?php


class GW_Classificators extends GW_Data_Object
{
	function findByGroup($groupKey)
	{
		$group = GW_Classificator_Types::singleton()->find(['`key` =?', $groupKey]);
		
		if($group)
			return $this->findAll(['`type`=?', $group->id],['key_field'=>'id']);
	}

}