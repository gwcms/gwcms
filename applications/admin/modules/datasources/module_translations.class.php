<?php


class Module_Translations extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		//if(!isset($this->list_params['order']))
		//	$this->list_params['order'] = "";
		
		if(isset($_GET['transsearch']))
		{
			list($module, $key) = GW_Translation::fullkeyToModAndKey($_GET['transsearch']);
			
			$this->replaceFilter("module", $module, "EQ");		
			$this->replaceFilter("key", $key, "EQ");	
			unset($_GET['transsearch']);
			$this->app->jump();
		}
		
		$this->options['module'] = GW_Array_Helper::buildOpts(GW_Translation::singleton()->getDistinctVals('module'));		
	}
	

	
	
	function appendData(&$data, $file, $module)
	{
		$sitelangs = GW::s('LANGS');
		
		$gdata = GW_Lang_XML::getAllLn($sitelangs, $file);

		foreach($gdata as $ln => $tmpdat)
		{
			$gdata[$ln] = GW_Array_Helper::arrayFlattenSep('/', $tmpdat);
		}

		foreach($sitelangs as $lncode){
				$i=0;
				foreach($gdata[$lncode] as $key => $row){
					$entry =& $data[$module.'/'.$key];


					$entry['module']=$module;
					$entry['key']=$key;


					foreach($sitelangs as $lncode){
						
						if($gdata[$lncode][$key]!='%NOT SPECIFIED%')
							$entry['value_'.$lncode] =  $gdata[$lncode][$key];
					}


					$entry['priority']=$i;
					$i++;
				}

			break;//tik pirma kalba paimti
		}		
		
	}
	
	function __importReadLangFiles()
	{
		$data = [];
		$list=glob(GW::s('DIR/APPLICATIONS').'site/lang/*.lang.xml');
		
		foreach($list as $file)
		{
			$globalid = explode('.lang',pathinfo($file, PATHINFO_FILENAME));
			$globalid = $globalid[0];
			
			$this->appendData($data, $file, 'G/'.$globalid);
		}
		
		$list2=glob(GW::s('DIR/APPLICATIONS').'site/modules/*/lang.xml');
		
		foreach($list2 as $file)
		{
			$globalid = explode('/lang.xml',$file);
			$globalid = explode('site/modules/',$globalid[0]);
			$globalid = $globalid[1];
					
			$this->appendData($data, $file, 'M/'.$globalid);
		}
		
		return $data;
	}
	
	function __importReadDb()
	{
		return GW_Translation::singleton()->findAll();		
	}
	
	function viewSynchronizeFromXml()
	{
		$trans_xml = $this->__importReadLangFiles();
		
		$trans_db = $this->__importReadDb();
		
		
		$counts=[
		    'translations in xml files'=>count($trans_xml), 
		    'translations stored in db'=>count($trans_db), 
		    'updated'=>0,
		    'inserted'=>0
		];
		
		//d::dumpas($trans_db);
		
		
		$sitelangs = GW::s('LANGS');
		
		
		$xml_not_found=[];
		$update_count = 0;
		
		$preview_changes = [];		
		
		//pereina duombazes irasus ikelia pokycius
		//ismeta nerastus 
		//importuoja naujas kalbas jei db tuscia verte
		
		foreach($trans_db as $item)
		{
			$index = $item->module.'/'.$item->key;
			$oldvals = $item->toArray();
			
			//jeigu xmluose nerasta skipina
			if(!isset($trans_xml[$index]))
			{
				$xml_not_found[]=$index;
				continue;
			}
				
			
			$xmlentry = $trans_xml[$index];
			
			foreach($sitelangs as $lncode)
			{
				$field='value_'.$lncode;
				if(!$item->$field && isset($xmlentry[$field])){
					$item->$field = $xmlentry[$field];
				}
			}
			
			$Oldpriority = $item->priority;
			
			//updeitina jei nesutampa tipai
			$item->priority = (string)$xmlentry['priority'];
			unset($trans_xml[$index]);
			
			if($item->changed_fields){
				
				if(isset($_GET['commit'])){
					$item->updateChanged();				
					$counts['updated']++;
					$counts['updated_keys']=[$item->module.'/'.$item->key];
				}else{
					foreach($item->changed_fields as $key => $x)
					{
						$preview_changes[$index.':'.$key]=['old'=>$oldvals[$key], 'new'=>$item->$key];
					}
				}
				
				
				
				
				
			}
		}
		
		$counts['Only in database']=count($xml_not_found);
		$counts['Only in database list']=$xml_not_found;
		
		// nerasti sukeliami
		
		foreach($trans_xml as $entry)
		{
			$item = GW_Translation::singleton()->createNewObject($entry);
			$item->save();
			$counts['inserted']++;
		}
		
		
		
		
		foreach($counts as $key => $val)
			$counts[$key] = json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			
		$this->tpl_vars['changes'] = $preview_changes;
		$this->tpl_vars['results'] = $counts;
		
		
		//d::ldump(['xmlnotfound'=>$xml_not_found]);
		
		
		
	}
	
	function getListConfig()
	{
		$cfg = array('fields' => [
			'id'=>'lof',
			'module'=>'Lof',
			'key'=>'Lof',
			]
		);
		
		
		foreach(GW::s("LANGS") as $lang)
			$cfg["fields"]["value_".$lang]="Lof";
			
		
		$cfg["fields"]['update_time'] = 'lof';
		$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	



	function viewKeySearch()
	{
		$i0 = GW_Translation::singleton();
		
		if(isset($_GET['q'])){
			$search = "'%".GW_DB::escape($_GET['q'])."%'";
			
			$cond = "`key` LIKE $search";
			$cond .= " OR `module` LIKE $search";
			foreach(GW::s("LANGS") as $lang)
				$cond .= " OR value_{$lang} LIKE $search";
			


			//$cond = "$cond";
		}

		if(isset($_REQUEST['ids']))
		{
			$tmp = trim($_REQUEST['ids'],'"');
			echo json_encode(['items'=>[['id'=>$tmp, 'title'=>$tmp]]]);
			exit;
		}
		
		//d::dumpas($cond);
		
		$page_by = 30;
		$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
		$params['offset'] = $page_by * $page;
		$params['limit'] = $page_by;
		//$params['select'] = '`key`, `module`';
	
		
		$list0 = $i0->findAll($cond, $params);
		
		$list=[];
		
		foreach($list0 as $item){
			
			$tmp=['id'=>$item->fullkey(), "title"=>$item->fullkey() ];
			
			$footer = [];
			foreach(GW::s("LANGS") as $lang)
				$footer[] = "$lang: ".GW_String_Helper::truncate($item->get("value_".$this->app->ln));
				
			$tmp['footer'] = implode('<br />',$footer);
				
			$list[] =$tmp;
		}
		
		$res['items'] = $list;
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
		

		
		echo json_encode($res);
		exit;
	}


	function viewForm()
	{
		//called from input_transkey
		if(isset($_GET['key']))
		{			
			if($itm = $this->model->findByFullKey($_GET['key']))
			{
				unset($_GET['key']);
				$_GET['id'] = $itm->id;
				$this->app->jump(false, $_GET);
			}
		}
		
		return parent::viewForm();
	}
	
	
	function doSaveTrans()
	{
		list($module, $key) = GW_Translation::fullkeyToModAndKey($_REQUEST['key']);
		
		$i0 = GW_Translation::singleton();
		$lang = str_replace('/[^a-z]/','',$_REQUEST['ln']);
		$trans = $i0->find(["`key`=? AND `module`=?", $key, $module]);
		
		if(!$trans){
			$t = $i0->createNewObject(['key'=>$key,'module'=>$module, "value_$lang"=>$_REQUEST['new_val']]);
			$t->insert();
			$method = "insert";
		}else{
			$trans->saveValues(["value_$lang"=>$_REQUEST['new_val']]);
			$method = "update";
		}
		
		$replace_what = GW::s("SITE_URL");
		
		$resp = ['status'=>"ok", 'method'=>$method];
		
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV)
		{
			initEnviroment(GW_ENV_PROD);
			$url = GW::s("SITE_URL").$_SERVER['REQUEST_URI'].'?'. http_build_query($_POST);			
			$resp['prod_request'] = $url;
		}
			
		
		
		
		
		
		die(json_encode($resp));
	}
	
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
}
