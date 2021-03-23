<?php


class Module_Page_Views extends GW_Common_Module
{
	public $filterpaths=false;
	
	function init()
	{
		$this->model = new GW_Adm_Page_View();//nebutinas uzloadina per lang faila jei nurodyta
		parent::init();
		
		$this->app->carry_params['filterpaths']=1;		
		$this->app->carry_params['clean']=1;		
		$this->app->carry_params['path']=1;
		
		
		$this->modid = $this->app->path_arr['1']['data_object_id'] ?? false;
		
		$page = GW_ADM_Page::singleton()->find(['id=?', $this->modid]);
				
		if($page){
			$_GET['path'] = $page->path;
		}
		
		
		if(isset($_GET['filterpaths']))
		{
			$this->filterpaths=explode(',',$_GET['filterpaths']);
		}
		
		if(isset($_GET['path'])){
			$inf = $this->app->getModulePathInfo($_GET['path']);
			$pc = array_values($inf['path_clean']);
			
			$this->filterpaths=[
				implode('/',$inf['path']),
				implode('/',$inf['path_clean']),
				$pc[0].'/'.$pc[count($pc)-1]
				];
			
		}
		if($this->filterpaths)
			$this->options['path'] = GW_Array_Helper::buildOpts($this->filterpaths);
	}
	
	
	function __eventAfterListParams(&$params)
	{		
		if($this->filterpaths)
			$params['conditions'] = GW_DB::mergeConditions ($params['conditions'], GW_DB::inConditionStr ("path", $this->filterpaths));
	}	
	
	function doInvertField() 
	{
		if(! $item = $this->getDataObjectById())
			return false;
        
		
		//kitiems to paties path atjungti default 
		if($_GET['field']=='default')
		{
			foreach(GW_Adm_Page_View::singleton()->findAll(["`default`=1 AND `path`=?", $item->path]) as $pview)
				$pview->saveValues(['default'=>0]);
		}
		

		if(!$item->invert($_GET['field'])) 
			return $this->setError('/g/GENERAL/ACTION_FAIL'); 
		
		
		
	
		$this->jump(); 
	}	
	
	
	
	function doMigrate()
	{
		$list = GW_ADM_Page::singleton()->findAll();
		
		$calc=['views'=>0,'orders'=>0];
				
		foreach($list as $page)
		{
			if(is_array($page->VIEWS))
			foreach($page->VIEWS as $vals)
			{
				$pview = new GW_Adm_Page_View;
				$pview->path = $page->path;
				
				
				$pview->title = $vals['name'];
				$pview->condition = $vals['conditions'];
				$pview->calculate  =  $vals['calculate'];
				$pviev->default = $vals['default'];
				$pview->order = $vals['order'];
				$pview->active = 1;
				
				$pview->insert();
				$calc['views']++;
			}
			
			
			
			if(is_array($page->ORDERS))
			foreach($page->ORDERS as $vals)
			{
				$pview = new GW_Adm_Page_View;
				$pview->path = $page->path;
				$pview->title = $vals['name'];
				$pview->condition = $vals['conditions'];
				$pview->calculate  =  $vals['calculate'];
				$pviev->default = $vals['default'];
				$pview->order = $vals['order'];
				$pview->active = 1;
				
				$pview->type="order";
				
				$pview->insert();
				$calc['orders']++;
			}
			
			
			
			$page->views="";
			$page->orders="";
			$page->updateChanged();
			
			
			
			
			
			
			
			
		}
		
		d::dumpas($calc);
		exit;
	}
	
	
	function __eventBeforeSave0($item)
	{
		if(isset($item->content_base['order_enabled']) && !$item->order_enabled)
			$item->order = "";

		if(isset($item->content_base['condition_enabled']) && !$item->condition_enabled)
			$item->condition = "";
		
		if(isset($item->content_base['fields_enabled']) && !$item->fields_enabled)
			$item->fields = "";
		
		if(isset($item->content_base['pageby_enabled']) && !$item->pageby_enabled)
			$item->page_by = 0;
		
		
		unset($item->content_base['order_enabled']);
		unset($item->content_base['condition_enabled']);
		unset($item->content_base['fields_enabled']);
		unset($item->content_base['pageby_enabled']);
		
	}

}

?>
