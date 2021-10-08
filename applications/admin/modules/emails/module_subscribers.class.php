<?php


class Module_Subscribers extends GW_Common_Module
{	
	

	use Module_Import_Export_Trait;		
	
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
			$list[GW::l("/A/FIELDS/$name")] = $name;
		
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
			$item->groups_string = isset($item->groups) && $item->groups ? implode(',', $item->groups): '';
			
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
	
	function viewExportSimple()
	{
		$ids = explode(',',$_POST['ids']);
		
		$ids = array_map('intval', $ids);
		
		$rows = [];
		
		if($ids){
			$cond = GW_DB::inCondition('id', $ids);		

			$list =  $this->model->findAll($cond);


			foreach($list as $item)
			{
				$row = [$item->name.' '.$item->surname, $item->email, $item->lang];
				$row = array_map(function ($str) { return str_replace(';',',', $str); }, $row);
				$rows[] = implode(';',$row);
			}
		}
		
		echo implode("\n", $rows);
		exit;
	}
	
	function viewImportSimple()
	{
		ob_start();
		ini_set('html_errors', false);
		
		$t = new GW_Timer;
		
		if(isset($_POST['rows'])){
			$rows = trim($_POST['rows']);
			$rows = str_replace("\r","\n",$rows);
			$rows = explode("\n", $rows);
		}elseif(isset($_POST['jsonrows'])){
			$rows = json_decode($_POST['jsonrows'], true);
		}
		
		$list = [];
		$rowbyeml = [];
		$emails = [];
		$failedrows = [];
		$insertcnt = 0;
		$existingcnt = 0;
		$failcnt = 0;
		
		foreach($rows as $row0)
		{
			if(isset($row0['email'])){
				//jsonrows
				$name = $row0['name'];
				$email = $row0['email'];
				$lang = $row0['lang'];
				$row = $row0;
			}else{
				//csv
				$row = explode(';', $row0);
				$name = trim($row[0] ?? '');
				$email = trim($row[1] ?? '');
				$lang = trim($row[2] ?? '');
			}
			
			$nameexpl = explode(' ', $name, 2);
			$name = $nameexpl[0] ?? '';
			$surname = $nameexpl[1] ?? '';			
			
			$item = ["name"=>$name, "surname"=>$surname, "email"=>$email, "lang"=>trim($lang)];
			
			$list[$email] = $item;
			$rowbyeml[$email] = $row;
			
		}
		
		$cond = GW_DB::inConditionStr('email', array_keys($list));		
		$existing =  $this->model->findAll($cond);		
		
		$ids = [];
		
		foreach($existing as $item)
		{
			unset($list[$item->email]);
			$ids[] = $item->id;
			$existingcnt++;
		}
		
		foreach($list as $idx => $vals){
			$item = $this->model->createNewObject();
			$item->setValues($vals);
			
			if($item->validate())
			{
				$item->insert();
				$insertcnt++;
				$ids[] = $item->id;
			}else{
				$failedrows[] = implode(';',$rowbyeml[$idx]);
				$failcnt++;
			}
		}
		

		$failedrows = implode("\n", $failedrows);
		
		$info = ["message"=>"Items created: $insertcnt, Existing items found: $existingcnt; failed: $failcnt, took: ".$t->stop()." secs", "failedrows"=>$failedrows, 'ids'=>$ids, 'failcnt'=>$failcnt];
		
		$errors = ob_get_contents();
		ob_clean();
		
		echo json_encode($info+['errors'=>$errors]);
		exit;
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
		$skipped_cnt=0;
		

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
				
				
				if($_REQUEST['insert_only']){
					$skipped_cnt++;
					$debug_data['insert']=$this->lang['SKIPPED'];
				}else{
					$item->id = $tmp->id;
					
					
					if($_REQUEST['update_name_surname_only'])
					{
						$updates = [];
						if($item->name) $updates[]='name';
						if($item->surname) $updates[]='surname';
						
						if($updates){
							$item->update($updates);
							$debug_data['insert']=$this->lang['UPDATED_NAME'];
						}else{
							$debug_data['insert']=$this->lang['SKIPPED'];
						}
						
					}else{
						$item->update();
						
						$debug_data['insert']=$this->lang['UPDATED'];
					}
					
					
					$update_cnt++;
					
				}
			}else{
				$item->insert();
				$insert_cnt++;
				$debug_data['insert']=$this->lang['INSERTED'];
			}
			
			
			$debug_table[] = $debug_data;

		}

		
		$msg="Sėkmingai importuota. Atnaujinti įrašai: $update_cnt; Nauji įrašai: $insert_cnt; Praleisti: $skipped_cnt";
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
		

		$sett = $_POST['item'];
		
		foreach($emails as $email){
	
			
			$sub = GW_NL_Subscriber::singleton()->createNewObject();
			$sub->email = $email['email'];
			$sub->lang = $sett['lang'];
			$sub->active = $sett['active'];
			if(!$sub->validate()){
				$this->setError($email['email'].": ".array_values($sub->errors)[0]);
			}else{
				$sub->insert();
				$this->setMessage(GW::l('/m/ADD_SUCCESS').' '.$email['email']);
			}
			
		}
		$this->app->jump();	
		//$this->tpl_vars['result'] = addslashes($data);
	}
	
	function viewEmailsFromText()
	{
		$this->tpl_vars['item'] = isset($_POST['item']) ? (object)$_POST['item'] : new stdClass(); 
	}
	
	
	
	function viewSearch()
	{
		$i0 = $this->model;
		
		
		if(isset($_GET['q']))
		{
			$search = "'%".GW_DB::escape($_GET['q'])."%'";		
			$cond = "(name LIKE $search OR surname LIKE $search OR email LIKE $search)";	
			
			$page_by = 30;
			$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
			$params['offset'] = $page_by * $page;
			$params['limit'] = $page_by;			
		}elseif(isset($_POST['ids'])){
			
			$ids = json_decode($_POST['ids'], true);
			
			if(!is_array($ids))
				$ids = [$ids];
			
			$ids = array_map('intval', $ids);
			$cond = GW_DB::inCondition('id', $ids);
		}	
		
		//$params['joins']=[
		//    ['left','mt_passengers AS psng','a.passenger_id=psng.id'],
		//];
		
		$params['select']='a.*';
		
		$list0 = $i0->findAll($cond, $params);
		
		$list=[];
		
		foreach($list0 as $item)
			$list[]=['id'=>$item->id, "title"=>$item->title.'('.$item->lang.')'];
		
		$res['items'] = $list;
		
		
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
		

		
		echo json_encode($res);
		exit;
	}


	function doCleanupRecipients()
	{
		$changed = 0;
		$duplicatesremoved=0;
		
		foreach($this->model->findAll() as $item){
			$item->name = trim($item->name);
			$item->surname = trim($item->surname);
			$item->email = trim($item->email);
			
			if($item->changed_fields){
				if(isset($item->changed_fields['email']) && $this->model->find(['email=? AND id!=?', $item->email,$item->id])){
					$item->delete();
					$duplicatesremoved++;
				}else{
					$item->updateChanged();
					$changed++;
				}
			}
		}
		
		$this->setMessage("Fixed:$changed. Duplicates removed:$duplicatesremoved");
		$this->jump();
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();

		$cfg['fields']['title']='L';

		//$cfg['fields']['progress']="L";
		
		
		return $cfg;
	}	
	
}
