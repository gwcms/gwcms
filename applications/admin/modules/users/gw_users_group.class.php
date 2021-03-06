<?php


class GW_Users_Group extends GW_Data_Object
{
	
	var $root_group_id=1;
	var $table = 'gw_users_groups';
	
	function delete()
	{
		if($this->get('id')==$this->root_group_id) //prevent to delete root group
			return false;
			
		return parent::delete();
	}
	
	/**
	 * can user view,edit,this item
	 * @param GW_User
	 */
	function canBeAccessedByUser($user)
	{
		if($user->isRoot())
			return true;

		if($this->get('id') == $this->root_group_id)
			return false;
			
		return true;
	}
	
	function getOptions()
	{
		//$cond = $active ? 'active!=0 AND removed=0' : '';
		
		return $this->getAssoc(Array('id','title')/*, $cond*/);
	}	
	
}