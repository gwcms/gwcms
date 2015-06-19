<?php


class Module_Subscribers extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->options['groups']=GW::getInstance('GW_NL_Group')->getOptions();
		
		$this->__prepareImportFields();
		
	}

	
	function viewDefault()
	{
		return $this->viewList();
	}
	
	
	function overrideFilterGroups($value)
	{
		
		
		$ids = array_filter($value,'intval');
				
		
		/* FIND BY TITLE
		 * //$value = "%$value%";
		$ids = GW::getInstance('Sms_Contact_Group')->getAssoc(['id','title'], ['user_id=? AND title LIKE ?',$this->app->user->id, $value]);
		
		if(!$ids)
			return "";
		$ids = array_keys($ids);
		
		*/
		
		$cond = " (SELECT count(*) FROM gw_nl_subs_bind_groups WHERE subscriber_id=id AND group_id IN (".implode(",",$ids)."))>0 ";
		
		
		return $cond;
	}
	
	
	
	var $import_field_translations=['email','name','surname','groups_string','lang','active','unsubscribed'];
	
	function __prepareImportFields()
	{
		$list=[];
		foreach($this->import_field_translations as $name)	
			$list[$this->app->fh()->FieldTitle($name)] = $name;
		
		$this->import_field_translations = $list;
	}
	
	function viewImport()
	{
		
	}	
	
	function viewExport()
	{
		$this->setListParams($cond, $params);
		$list = $this->model->findAll($cond, $params);
		
		$data = "";
		
		
		$data = implode("\t", array_keys($this->import_field_translations))."\n";
		
		foreach($list as $item){
			$item->groups_string = implode(',', $item->groups);
			
			
			$row = Array();
			foreach($this->import_field_translations as $xlsname => $sysname)
				$row[] = str_replace("\n","<br />", $item->get($sysname));
			
			$data .= implode("\t", $row)."\n";
		}
		
		return compact('data');
	}
	
	function doImport()
	{
		$field_translations=$this->import_field_translations;


		$debug_table = Array();
		$debug_table1 = Array();
		$data = $_REQUEST['data'];

		$data = explode("\n", $data);
		
	

		foreach($data as $i=>$row)
		{
			$data[$i] = explode("\t", trim($row, "\r"));
		}

		$header = array_shift($data);
		$translated_header=Array();

		

		
		//translate fields
		foreach($header as $column_name)
		{
			$translated_header[]=$field_translations[$column_name];

			if(!$field_translations[$column_name])
				die("Neatpažintas stulpelis: $column_name");
		}

		$update_cnt=0;
		$insert_cnt=0;
		

		foreach($data as $line => $row)
		{
			$item = $this->model->createNewObject();


			foreach($translated_header as $i => $fieldname)
				$item->set($fieldname, $row[$i]);


			if(!$item->email || !$item->lang)
			{
				dump("Blogas įrašas eilutėje $line (".print_r($row,true).')');
				continue;
			}
			
			$item->groups = explode(',', $item->groups_string);
			unset($item->content_base['groups_string']);

			
			$debug_data = $item->toArray();
			
			if($tmp=$item->find(Array('email=?',$item->email))){
				$item->id = $tmp->id;
				$item->update();
				$update_cnt++;
				$debug_data['insert']=$m->lang['UPDATED'];
			}else{
				$item->insert();
				$insert_cnt++;
				$debug_data['insert']=$m->lang['INSERTED'];
			}
			
			
			$debug_table[] = $debug_data;

		}

		
		$msg="Sėkmingai importuota. Atnaujinti įrašai: $update_cnt; Nauji įrašai: $insert_cnt";
		echo $msg;
		

		$this->smarty->assign('debug_table', $debug_table);

	}	
	
	
}
