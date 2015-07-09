<?php


class GW_NL_Subscriber extends GW_Composite_Data_Object
{
	public $table = 'gw_nl_subscribers';
	public $calculate_fields = ['title'=>'getTitle'];
	
	public $validators = [
	    'email'=>['gw_email', ['required'=>1]],
	    'lang'=>['gw_string', ['required'=>1]],
	];
	
	
	function validate()
	{
		parent::validate();
						
		if($this->count(Array('email=? AND id!=?', $this->email, $this->id)))
			$this->errors['email']='/VALIDATION/UNIQUE';
				
		return $this->errors ? false : true;	
	}	
	
	var $composite_map = Array
	(
		'groups' => ['gw_links', ['table'=>'gw_nl_subs_bind_groups', 'fieldnames'=>['subscriber_id','group_id'], 'get_cached'=>1]],
	);
	
	function getTitle()
	{
		return $this->email ? "$this->name $this->surname &lt;$this->email&gt;" : '';
	}
	
	function setConfirmCode()
	{
		$this->set('confirm_code', rand(1000000000, 4294967295));
	}
	
	

}			



