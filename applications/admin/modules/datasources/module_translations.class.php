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
			list($group,$module, $key) = explode('/',$_GET['transsearch'],3);
			$module = $group."/".$module;
			
			$this->setFilter("module", $module, "EQ");		
			$this->setFilter("key", $key, "EQ");	
			unset($_GET['transsearch']);
			$this->app->jump();
		}
		
		
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
