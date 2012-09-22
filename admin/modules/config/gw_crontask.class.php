<?


class GW_CronTask extends GW_Data_Object
{
	var $table = 'gw_crontasks';
	

	function getAllTimeMatches()
	{
		$times = GW::$db->fetch_one_column("SELECT DISTINCT time_match FROM `{$this->table}` WHERE active =1",'time_match');
	
		return $times;
	}
	
	function getByTimeMatch($tm)
	{
		return $this->findAll(Array('active=1 AND time_match=?',$tm));
	}
	
	function execute()
	{
		GW_Task::addStatic($this->name);
	}
	
	function getByTimeMatchExecute($tm)
	{		
		$list = $this->getByTimeMatch($tm);
				
		foreach($list as $item)
			$item->execute();
	}	
	
}