<?php


class GW_NL_Message extends GW_i18n_Data_Object
{
	public $table = 'gw_nl_messages';

	public $encode_fields = ['groups'=>'json', 'sent_info'=>'jsono','recipients_ids'=>'jsono'];
	public $calculate_fields = ['body_full'=>'getBodyFull'];
	
	public $validators = [
	    'title'=>['gw_string', ['required'=>1]],
	    //'sender'=>['gw_string', ['required'=>1]],
	    'subject'=>['gw_string', ['required'=>1]],
	    'lang'=>['gw_string', ['required'=>1]],
	];	
	
	public $ownerkey = 'emails/messages';
	public $extensions = ['attachments'=>1];
	public $i18n_fields = [
	    "subject"=>1,
	    "body"=>1,
	    "sender"=>1,
	    "recipients_count"=>1,
	    'lang'=>1
	];
	
	
	public function getRecipients()
	{
		/*
		$ids=$this->groups;
		
		if(!$ids)
			return [];
		

		
		//$groups_cond= "(SELECT count(*) FROM gw_nl_subs_bind_groups WHERE subscriber_id=id AND group_id IN (".implode(',', $ids).")) > 0";
		
		
		//$r = GW::getInstance('GW_NL_Subscriber')->count(['active=1 AND unsubscribed=0 AND lang=? AND '.$groups_cond, $this->lang]);
		
		return $r;
		 * 
		 */
	}
	
	
	public function getRecipientsCount()
	{
		$cond = GW_DB::inCondition('group_id', $this->groups);
		
		$cnt = $this->getDB()->fetch_result($q="SELECT count(DISTINCT id) FROM `gw_nl_subscribers` AS a, `gw_nl_subs_bind_groups` AS b WHERE a.id=b.subscriber_id AND $cond");
		
		return $cnt;
	}
	
	
	function getBodyFull($field = 'body')
	{
		return '<html><head><meta charset="UTF-8"></head><body style="margin:0">'.$this->$field.'</body></html>';
	}
	
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case "BEFORE_SAVE":
				if($this->recipients_ids){
					$ids= $this->recipients_ids;
					$ids = array_map('intval', $ids);
					$this->recipients_ids = $ids;
				}
			break;
			
		}
		
		parent::eventHandler($event, $context_data);
		
	}
	
	
	function getActiveLangs()
	{
		$langs = [];
		foreach(GW::s('LANGS') as $ln){
			if($this->get('lang', $ln)==1)
				$langs[] = $ln;
		}
		
		return $langs;
	}
	
}			