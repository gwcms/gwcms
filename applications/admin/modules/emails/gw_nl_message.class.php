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
	
	
	function getRecipients($portion,  $lang, $count_total=false)
	{
		$db =& $this->getDB();
		
		$grp_cond = " FALSE ";
		
		if($this->groups){
			$grp_incond = GW_DB::inCondition('b.`group_id`', $this->groups);
			$grp_cond = "a.active=1 AND (a.confirm_code IS NULL OR a.confirm_code < 100) AND $grp_incond";
		}
		
		
		$separete_ids = $this->recipients_ids;
		$part_incond = " FALSE ";
		
		if($separete_ids){
			
			$part_incond = GW_DB::inCondition('a.`id`', $separete_ids);
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT a.id, a.* 
			FROM 
				`gw_nl_subscribers` AS a
			LEFT JOIN `gw_nl_subs_bind_groups` AS b
				ON a.id=b.subscriber_id
			LEFT JOIN gw_nl_sent_messages AS aa 
				ON a.id = aa.subscriber_id AND aa.message_id=?
			WHERE 
				a.unsubscribed=0 AND
				a.lang=? AND
				( ($grp_cond) OR ($part_incond) )
				". (!$count_total ? 'AND aa.status IS NULL' : '')."
			LIMIT $portion
			";
		
		$sql = GW_DB::prepare_query([$sql, $this->id, $lang]);
		
		$rows = $db->fetch_rows($sql);
		
		//d::dumpas([$rows, $sql, $lang]);
		
		
		if($count_total){
			
			$count_total= $db->fetch_result("SELECT FOUND_ROWS()");
			
			return $count_total;
		}
		
		
		
		
		return $rows;
	}
	
	function updateRecipientsCount()
	{
		foreach(GW::s('LANGS') as $ln){
			if($this->get('lang', $ln)==1){
				$this->set('recipients_count', $this->getRecipients(1, $ln, true), $ln);
			}else{
				$this->set('recipients_count', 0, $ln);
			}
		}		
	}
	
}			