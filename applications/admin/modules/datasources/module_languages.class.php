<?php


class Module_Languages extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		$cfg['fields']['img_png'] = 'L';
		$cfg['fields']['img_css'] = 'L';
		

		

		
		
		return $cfg;
	}

		
	function getOptionsCfg()
	{		
		$opts = [
			'title_func'=>function($item){ return isset($_GET['native']) ? $item->native_name:$item->name .' ('.$item->get('trcode').')';  },
			'search_fields'=>['iso639_1','trcode','name','native_name']			
		];	
		

		
		if(isset($_GET['byCode'])){
			$opts['idx_field'] = 'iso639_1';
		}
		
		if(isset($_GET['byTranslCode'])){
			$opts['idx_field'] = 'trcode';
		}
		
		return $opts;	
	}			
		
		
	function viewForm()
	{
		//if idkey present instead of id
		if(isset($_GET['idkey']))
		{			
			if($itm = $this->model->find(['iso639_1 =? ',$_GET['idkey']]))
			{
				unset($_GET['idkey']);
				$_GET['id'] = $itm->id;
				$this->app->jump(false, $_GET);
			}
		}
		
		return parent::viewForm();
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
