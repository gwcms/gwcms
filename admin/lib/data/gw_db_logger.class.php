<?
class GW_DB_Logger
{
	
	static public $table = 'gw_log';
	
	/**
	 * first argument can be either array either as msg in past case use other params
	 * to define log entry
	 * type = modem | user | proc.ctrl | mysql | frontend | backend
	 */
	function msg($entry=Array(),$type='',$action='',$status='',$add_info){
		
		
		if(!is_array($entry))
			$entry=Array('msg'=>$entry);
		
		if(!$entry['time'])$entry['time']=date('Y-m-d H:i:s');
		if(!$entry['type'])$entry['type']=$type;
		if(!$entry['action'])$entry['action']=$action;
		if(!$entry['status'])$entry['status']=$status;
		
		if(!$entry['add_info'])$entry['add_info']=$add_info;
		if(is_array($entry['add_info']))$entry['add_info']=serialize($entry['add_info']);
			
		GW::$db->insert(self::$table,$entry);
	}
}
