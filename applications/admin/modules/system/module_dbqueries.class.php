<?php


class Module_DBQueries extends GW_Common_Module
{		
	function doExecuteQuery()
	{
		$item = $this->getDataObjectById();

		if(!$item->get('active'))
			return $this->setError("Can't run. Switch query state to active");

		$this->execSqls($item->get('sql'), true);
	}
	
	function execSqls($sqls, $show_exec_res=true)
	{
		$sqls = explode(';', $sqls);
		
		$db =& $this->app->db;
		
		$results;
		
		foreach($sqls as $sql)
		{
			$sql = trim($sql);
			if(!$sql)continue;
			
			$result = ['sql'=>htmlspecialchars($sql)];
			
			
			$res = $db->fetch_rows($sql, true, true);
			$result['res']= json_encode($res, JSON_PRETTY_PRINT);
			$result['affected'] = $db->affected();
			$result['error'] = $db->error; 
			$results[] = $result;
		}	
		
		if($show_exec_res)
			echo  GW_Data_to_Html_Table_Helper::doTable($results);
		
		return $results;
	}
	
}
