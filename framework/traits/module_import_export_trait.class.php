<?php

trait Module_Import_Export_Trait
{	
	
	function __importExportGetCols()
	{
		$cols = $this->model->getColumns();
		
		foreach($cols as $col => $d)
			$cols[$col] = $col;
		
		return $cols;
	}
	
	function __list2Str($list)
	{
		$data = "";
		
		$cols = $this->__importExportGetCols();

		$data = implode("\t", array_keys($cols))."\n";
		
		foreach($list as $item){
			
			
			$row = Array();
			foreach($cols as $xlsname => $sysname)
				$row[] = str_replace("\n","<br />", isset($item->$sysname) ? $item->$sysname:'' );
			
			$data .= implode("\t", $row)."\n";
		}
		
		return $data;
	}
	
	function viewExportData()
	{
		$this->setListParams($cond, $params);
		$list = $this->model->findAll($cond, $params);
		
		$data = $this->__list2Str($list);
				
				
		$this->tpl_file_name=GW::s("DIR/".$this->app->app_name."/TEMPLATES").'tools/generic_export';
		$this->tpl_vars['data']=$data;
		$this->tpl_vars['fields'] = $this->__importExportGetCols();
	}
	
	function viewImportData()
	{
		$this->tpl_vars['fields'] = $this->__importExportGetCols();
		
		$this->tpl_file_name=GW::s("DIR/".$this->app->app_name."/TEMPLATES").'tools/generic_import';
	}
	
	function doImportData()
	{
		$rawdata = $_REQUEST['data'];

		$data = explode("\n", $rawdata);
		
	
		
		foreach($data as $i=>$row)
		{
			$data[$i] = explode("\t", trim($row, "\r"));
		}
		
		$header = array_shift($data);
		$translated_header=Array();

		
		$cols = $this->model->getColumns();
		
		foreach($cols as $col => $d)
			$cols[$col] = $col;
		
		$counts = ['success'=>0,'fail'=>0];
		
		
		$error_rows=[];
		
		foreach($data as $line => $row)
		{
			$item = $this->model->createNewObject();


			foreach($header as $i => $fieldname)
				$item->set($fieldname, $row[$i]);
			
			if(!$item->validate())
			{
				$error_rows[] = [$row, $item->errors];
				$counts['fail'] ++;
			}else{
				
				$item->save();
				$counts['success'] ++;
			}
		}
		
		d::ldump($counts);
		
		d::ldump(['error_rows'=>$error_rows]);
		
		$this->tpl_vars['data']=$rawdata;
		

	}
}