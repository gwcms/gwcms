<?php


class Module_DBQueries extends GW_Common_Module
{		
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
