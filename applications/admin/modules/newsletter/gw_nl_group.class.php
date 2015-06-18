<?php


class GW_NL_Group extends GW_Composite_Data_Object
{
	var $table = 'gw_nl_subs_groups';
	
	var $composite_map = Array
	(
		'subscribers' => ['gw_links', ['table'=>'gw_nl_subs_bind_groups', 'fieldnames'=>['group_id','subscriber_id'], 'get_cached'=>1]],
	);	
	
		
	function getOptions($active=true)
	{
		$cond = $active ? 'active!=0' : '';
		
		return $this->getAssoc(['id','title'], $cond);
	}
	
	function getCountsByIds($ids)
	{
		if(!$ids)
			return false;
		
		$ids_cond = "group_id IN (".implode(',', $ids).")";
		
		$counts_sql = "SELECT group_id, count(*) AS cnt 
			FROM gw_nl_subs_bind_groups AS b, gw_nl_subscribers AS a 
			WHERE a.id=b.subscriber_id AND $ids_cond AND a.active=1 AND a.unsubscribed=0
			GROUP BY group_id";
		
		return $this->getDb()->fetch_assoc($counts_sql);		
	}
	
	function getOptionsWithCounts()
	{
		$opt = $this->getOptions();
		
		if(!$opt)
			return $opt;
		
		
		
		$counts = $this->getCountsByIds(array_keys($opt));
		
		
		
		foreach($opt as $id => $title)
		{
			$opt[$id] = $title." (".(isset($counts[$id]) ? $counts[$id] : 0).")";
		}
		
		return $opt;
	}
	
}