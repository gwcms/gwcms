<?php


class Module_Articles extends GW_Common_Module
{	
	
	
	public $multisite = true;
	public $dynamic_fields = true;	
	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->options['group_id'] = GW_Articles_Group::singleton()->getOptions(false);
						
	}
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		//dont show at first time
		//foreach(['group_id','duration','insert_time','update_time','id'] as $field)
		//	$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		$cfg['fields']["image"] = "L";
		$cfg['fields']["short"] = "lof";
		$cfg['fields']["text"] = "lof";
		
		return $cfg;
	}	
	

	function __eventAfterList(&$list)
	{
		
	}

	
	
	//sutraukti informacija is meta tagu
	public $__fetchexternallinkinfo=[];
	function __eventBeforeInsert($item)
	{
		if($item->external_link && !$item->title){
			$info = GW_Misc_Helper::fetchMetaTags($item->external_link);
			$this->__fetchexternallinkinfo = $info;
			
			$this->setItemImageFromUrl($item, $info['image']);
			$item->title = $info['title'];
			$item->short = $info['description'];
			
			//pridet lauikeli
			//$item->icon = $info['icon'];
		

		}
		
		
		//d::dumpas($item);
	}
	
	function __eventAfterInsert($item)
	{
		if($this->__fetchexternallinkinfo){
			
			$url = $this->__fetchexternallinkinfo['icon'];
			
			$file = tempnam(GW::s('DIR/TEMP'), 'TMP_');
			
			file_put_contents($file, file_get_contents($url));
		
			$id = $item->extensions['attachments']->storeAttachment("attachments", $file, ['title'=>"icon"], basename($url));	
		}
				
		$item->extensions['attachments']->removeDuplicates();
	}
	
	function __eventAfterForm($item)
	{
		//d::ldump($item->getAttachmentByLtTitle('icon'));		
	}
		
	
	
 
}
