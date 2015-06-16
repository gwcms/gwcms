<?php


class GW_NL_Groups extends GW_Data_Object
{
	var $table = 'gw_nl_subs_groups';
	
	
	function getOptions($active=true)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id','title'], $cond);
	}
	
	function getOptionsWithCounts()
	{
		$opt = $this->getOptions();
		
		if(!$opt)
			return $opt;
		
		$ids_cond = "group_id IN (".implode(',', array_keys($opt)).")";
		
		$counts_sql = "SELECT group_id, count(*) AS cnt 
			FROM gw_nl_subs_bind_groups AS b, gw_nl_subscribers AS a 
			WHERE a.id=b.subscriber_id AND $ids_cond AND a.active=1 AND a.unsubscribed=0
			GROUP BY group_id";
		
		$counts = $this->getDb()->fetch_assoc($counts_sql);
		
		foreach($opt as $id => $title)
		{
			$opt[$id] = $title." (".(isset($counts[$id]) ? $counts[$id] : 0).")";
		}
		
		return $opt;
	}
	
}