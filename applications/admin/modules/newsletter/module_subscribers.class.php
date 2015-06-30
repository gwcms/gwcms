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
	
	
	function __list2Str($list)
	{
		$data = "";
		
		$data = implode("\t", array_keys($this->import_field_translations))."\n";
		
		
		foreach($list as $item){
			$item->groups_string = implode(',', $item->groups);
			
			$row = Array();
			foreach($this->import_field_translations as $xlsname => $sysname)
				$row[] = str_replace("\n","<br />", isset($item->$sysname) ? $item->$sysname:'' );
			
			$data .= implode("\t", $row)."\n";
		}
		
		return $data;
	}
	
	function viewExport()
	{
		$this->setListParams($cond, $params);
		$list = $this->model->findAll($cond, $params);
		
		$data = $this->__list2Str($list);
		
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
				$debug_data['insert']=$this->lang['UPDATED'];
			}elseif($_REQUEST['insert_only']){
				$skipped_cnt++;
				$debug_data['insert']=$this->lang['SKIPPED'];
			}else{
				$item->insert();
				$insert_cnt++;
				$debug_data['insert']=$this->lang['INSERTED'];
			}
			
			
			$debug_table[] = $debug_data;

		}

		
		$msg="Sėkmingai importuota. Atnaujinti įrašai: $update_cnt; Nauji įrašai: $insert_cnt";
		echo $msg;
		

		$this->smarty->assign('debug_table', $debug_table);

	}	
	
	
	function extractEmailAddress($string)
	{
		$emails = [];
		
		foreach (preg_split('/\s/', $string) as $token) 
		{
			$email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
			if ($email !== false) {
				$email = strtolower($email);
				$emails[$email] = ['email'=>$email];
			}
		}
		return $emails;
	}
	function extractEmailAddressWithName($string){
		
		preg_match_all("/'([^'@]*)' <(.*@.*)>/U", $string, $matches, PREG_SET_ORDER);
		
		
		$list = [];
		
		foreach($matches as $match)
		{
			$name_surname = explode(' ',$match[1],2);
			
			$name = $name_surname[0];
			
			$surname = (isset($name_surname[1]) && strlen($name_surname[1])>1) ? $name_surname[1] : '';
			
			$e = strtolower($match[2]);
			
			$list[$e] = ['email'=>$e, 'name'=>GW_String_Helper::ucfirst($name,1), 'surname'=>GW_String_Helper::ucfirst($surname,1)];
		}
		
		return $list;
	}
	

	function doParseEmailsFromText()
	{
		
		
		$string = $_POST['string'];
		
		$emails1 = self::extractEmailAddress($string);
		
		
		$this->tpl_vars['string'] = $string;
		
		
		$emails2 = self::extractEmailAddressWithName($string);
		
		$emails = array_merge($emails1, $emails2);
		
		//d::dumpas($_POST['item']);
		
		foreach($emails as $i => $item)
		{
			$emails[$i] = $item + $_POST['item'];			
			$emails[$i] = (object)$emails[$i];
		}
				
		$data = $this->__list2Str($emails);
				
		$this->tpl_vars['result'] = addslashes($data);
	}
	
	function viewEmailsFromText()
	{
		$this->tpl_vars['item'] = isset($_POST['item']) ? (object)$_POST['item'] : new stdClass(); 
	}
	
}
