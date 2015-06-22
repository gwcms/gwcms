<?php


class GW_NL_Message extends GW_Data_Object
{
	public $table = 'gw_nl_messages';

	public $encode_fields = Array('groups'=>'json', 'sent_info'=>'jsono');
	public $calculate_fields = ['body_full'=>'getBodyFull'];
	
	public $validators = [
	    'title'=>['gw_string', ['required'=>1]],
	    'sender'=>['gw_string', ['required'=>1]],
	    'subject'=>['gw_string', ['required'=>1]],
	    'lang'=>['gw_string', ['required'=>1]],
	];	
	
	
	public function getRecipients(){
		
		$ids=$this->groups;
		
		if(!$ids)
			return [];
		
		$groups_cond= "(SELECT count(*) FROM gw_nl_subs_bind_groups WHERE subscriber_id=id AND group_id IN (".implode(',', $ids).")) > 0";
		
		
		$r = GW::getInstance('GW_NL_Subscriber')->findAll(['active=1 AND unsubscribed=0 AND lang=? AND '.$groups_cond, $this->lang]);
		
		return $r;
	}
	
	function getBodyFull()
	{
		return "<html><body style='margin:0'>".$this->body."</body></html>";
	}
	
}			