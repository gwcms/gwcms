<?php


class Module_Translations extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
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
		$data = $this->__importReadLangFiles();
		
		$dbitems = $this->__importReadDb();
		
		
		$counts=[
		    'xml'=>count($data), 
		    'db'=>count($dbitems), 
		    'updated'=>0,
		    'inserted'=>0
		];
		
		//d::dumpas($dbitems);
		
		
		$sitelangs = GW::s('LANGS');
		
		
		$xml_not_found=[];
		$update_count = 0;
		
		foreach($dbitems as $item)
		{
			$index = $item->module.'/'.$item->key;
			
			if(!isset($data[$index]))
			{
				$xml_not_found[]=$index;
				continue;
			}
				
			
			$xmlentry = $data[$index];
			
			foreach($sitelangs as $lncode)
			{
				$field='value_'.$lncode;
				if(!$item->$field && isset($xmlentry[$field]))
					$item->$field=$xmlentry[$field];
			}
			
			$Oldpriority = $item->priority;
			
			//updeitina jei nesutampa tipai
			$item->priority = (string)$xmlentry['priority'];
			unset($data[$index]);
			
			if($item->changed_fields){
				$item->updateChanged();				
				$counts['updated']++;
				$updated[]=[$item->module,$item->key];
			}
		}
		
		$counts['xmlnotfound']=count($xml_not_found);
		
		
		foreach($data as $entry)
		{
			$item = GW_Translation::singleton()->createNewObject($entry);
			$item->save();
			$counts['inserted']++;
		}
		
		
		
		$this->app->setMessage(json_encode($counts));
		$this->app->setMessage("xmlnotfound: <br/>".implode("<br />", $xml_not_found));
		
		//d::ldump(['xmlnotfound'=>$xml_not_found]);
		
		
		
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
