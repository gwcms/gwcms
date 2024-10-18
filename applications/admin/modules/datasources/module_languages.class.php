<?php


class Module_Languages extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	


		
	function getOptionsCfg()
	{		
		$opts = [
			'title_func'=>function($item){ return isset($_GET['native']) ? $item->native_name:$item->name .' ('.$item->get('code').')';  },
			'search_fields'=>['iso639_1','name','native_name']			
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
	
	
	
	function doAddExtLanguage()
	{
		
		$sel=['type'=>'select','options'=>$this->app->langs, 'empty_option'=>1, 'options_fix'=>1, 'required'=>1];
		$limitinp=['type'=>'select','options'=>[100=>100,150=>150,200=>200,500=>500,1=>1,10=>10,50=>50]];
		$form = ['fields'=>['from'=>$sel, 'to'=>$sel, 'limit'=>$limitinp],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/SELECT_SOURCE_DEST_LANG'))))
			return false;	
		
		
		$code = "xx";
		
		$sql[] ="ALTER TABLE `gw_translations` ADD `value_$code` TEXT NOT NULL COMMENT 'addExtLanguage' AFTER `value_en`;";
		
		$sql[] = "CREATE TABLE `gw_i18n_$code` (
  `_type` smallint NOT NULL,
  `_id` bigint NOT NULL,
  `_field` smallint NOT NULL,
  `_value` text CHARACTER SET utf8mb3 COLLATE utf8mb3_lithuanian_ci NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_lithuanian_ci;";
		
		
	
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
