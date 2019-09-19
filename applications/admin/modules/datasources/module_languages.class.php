<?php


class Module_Languages extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	
	function viewOptions()
	{
		
		//$cond = GW_DB::buidConditions($this->filters);
		if(isset($_GET['native']))
		{
			$opts = $this->model->getOptionsNative();
		}else{
			$opts = $this->model->getOptions();
		}
		
		
		
				
		echo json_encode($opts);
		exit;
	}			
	
	
	function viewSearch()
	{
		$i0 = $this->model;
		$cond="";
		
		if(isset($_GET['q'])){
			$search = "'%".GW_DB::escape($_GET['q'])."%'";

			//OR title_ru LIKE $search
			$cond = "(`name` LIKE $search OR `native_name` LIKE $search )";
		}elseif(isset($_POST['ids'])){
			$ids = json_decode($_POST['ids'], true);
			if(!is_array($ids))
				$ids = [$ids];
			
			$ids = array_map('intval', $ids);
			$cond = GW_DB::inCondition('id', $ids);
		}
		
		
		$page_by = 30;
		$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
		$params['offset'] = $page_by * $page;
		$params['limit'] = $page_by;
	
		
		$list0 = $i0->findAll($cond, $params);
		
		$list=[];
		
		foreach($list0 as $item)
			$list[]=['id'=>$item->id, "title"=>$item->get("name").'('.$item->get('native_name').')'];
		
		$res['items'] = $list;
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
				
		echo json_encode($res);
		exit;
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
