<?php

/*
 * to use default list without GW_Data_Object
 * use GW_Dummy_Data_Object
 * example in 
 * 	admin/modules/config/module_tasks.class.php function viewProcesses()
 * 	AND admin/modules/config/tpl/tasks/processes.tpl
 */

class GW_Dummy_Data_Object extends GW_Data_Object {

	function buildList($list) {
		foreach ($list as $i => $vals)
			$list[$i] = $this->createNewObject($vals);

		return $list;
	}

}
