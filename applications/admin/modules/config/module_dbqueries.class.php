<?php

include_once __DIR__.'/gw_db_queries.class.php';

class Module_DBQueries extends GW_Common_Module
{	

	function init()
	{
		$this->model = new GW_DB_Queries();
		
		parent::init();
	}

	
	function viewDefault()
	{
		$this->viewList();
	}

	

	
	function doExecuteQuery()
	{
		$item = $this->getDataObjectById();

		if(!$item->get('active'))
			return $this->setErrors("Can't run. Switch query state to active");
		
		$sqls = explode(';', $item->get('sql'));
		
		$db =& $this->app->db;
		
		foreach($sqls as $sql)
		{
			if(!trim($sql))continue;
			
			print("<b>SQL</b>: ".htmlspecialchars($sql)."\n");
			$res = $db->fetch_rows($sql);

			
			if($res)
				echo GW_Data_to_Html_Table_Helper::doTable($res);
			else
				print("<b>No result</b>\n");
				
			print("<hr />");
		}
	}
}
