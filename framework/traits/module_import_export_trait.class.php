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
	
	public $importexport_replacibles=[["\n","\t","\r"],['\n','\t','\r']];
	
	function __list2Str($list)
	{
		$data = "";
		
		$cols = $this->__importExportGetCols();

		$data = implode("\t", array_keys($cols))."\n";
		
		foreach($list as $item){
			
			
			$row = Array();
			foreach($cols as $xlsname => $sysname){
				$val = isset($item->$sysname) ? $item->$sysname:'';
				$row[] = str_replace($this->importexport_replacibles[0], $this->importexport_replacibles[1], $val);
			}
			
			$data .= implode("\t", $row)."\n";
		}
		
		return $data;
	}
	
	function viewExportData()
	{
		$this->initListParams(false,'list');
		$this->setListParams($cond, $params);
		$list = $this->model->findAll($cond, $params);
		
		//d::Dumpas([$cond, $params]);
		
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
		$saved=[];
		
		foreach($data as $line => $row)
		{
			$item = $this->model->createNewObject();


			foreach($header as $i => $fieldname){
				$val = isset($row[$i]) ? $row[$i] : '';
				$val = str_replace($this->importexport_replacibles[1], $this->importexport_replacibles[0], $val);
				$item->set($fieldname, $val);
			}
			
			if(!$item->validate())
			{
				$error_rows[] = [$row, $item->errors];
				$counts['fail'] ++;
			}else{
				
				$item->save();
				$counts['success'] ++;
				$saved[]=$item->toArray();
			}
		}
		
		d::ldump($counts);
		d::ldump([
		    'error_rows'=>$error_rows,
		    'header'=>$header, 
		    'saved'=>$saved
		]);
		
		$this->tpl_vars['data']=$rawdata;
		

	}
}