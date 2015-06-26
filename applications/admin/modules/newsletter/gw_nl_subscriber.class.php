<?php


class GW_NL_Subscriber extends GW_Composite_Data_Object
{
	public $table = 'gw_nl_subscribers';
	public $calculate_fields = ['title'=>'getTitle'];
	
	public $validators = [
	    'email'=>['gw_email', ['required'=>1]],
	    'lang'=>['gw_string', ['required'=>1]],
	];	
	
	var $composite_map = Array
	(
		'groups' => ['gw_links', ['table'=>'gw_nl_subs_bind_groups', 'fieldnames'=>['subscriber_id','group_id'], 'get_cached'=>1]],
	);
	
	function getTitle()
	{
		return $this->email ? "$this->name $this->surname &lt;$this->email&gt;" : '';
	}
	
	
	
	

}			