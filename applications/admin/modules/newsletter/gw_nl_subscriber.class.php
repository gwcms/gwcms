<?php


class GW_NL_Subscriber extends GW_Composite_Data_Object
{
	public $table = 'gw_nl_subscribers';
	public $calculate_fields = ['title'=>'getTitle'];
	
	public $validators = [
	    'email'=>['gw_email', ['required'=>1]]
	];	
	
	var $composite_map = Array
	(
		'groups' => ['gw_links', ['table'=>'gw_nl_subs_bind_groups', 'fieldnames'=>['subscriber_id','group_id']]],
	);
	
	function getTitle()
	{
		return $this->email ? "$this->name $this->surname <$this->email>" : '';
	}
	
	
	
	

}			