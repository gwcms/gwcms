<?php
/*
https://europa.eu/european-union/about-eu/countries_en
https://www.worldatlas.com/aatlas/ctycodes.htm
*/

class Module_Countries extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	
	function viewOptions()
	{
		$i0 = $this->model;
		
		if(isset($_GET['q'])){
			$exact = GW_DB::escape($_GET['q']);
			$search = "'%".$exact."%'";

			//OR title_ru LIKE $search
			$title_cond = GW_Country::buildFieldCond('title', $search);
			$cond = "($title_cond  OR `aka` LIKE $search OR code = '$exact')";
		}elseif(isset($_REQUEST['ids'])){
			$ids = json_decode($_REQUEST['ids'], true);
			if(!is_array($ids))
				$ids = [$ids];
			
			if(isset($_GET['byCode'])){
				$cond = GW_DB::inConditionStr('code', $ids);
			}else{
				$ids = array_map('intval', $ids);
				$cond = GW_DB::inCondition('id', $ids);
			}
		}		
		
		$page_by = 30;
		$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
		$params['offset'] = $page_by * $page;
		$params['limit'] = $page_by;
	
		
		$list0 = $i0->findAll($cond ?? '', $params);
		
		$list=[];
		
		$idfield = isset($_GET['byCode']) ? 'code' : 'id';
					
		foreach($list0 as $item)
			$list[]=['id'=>$item->$idfield, "title"=>$item->get("title_".$this->app->ln).' ('.$item->get('code').')'];
		
		$res['items'] = $list;
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
				
		echo json_encode($res);
		exit;
	}	
	
	
	function doTranslate()
	{		
		$ln = $_GET['to'];
		$t = new GW_Timer;
		
		
		
		$list = $this->model->findAll("title_{$ln}=''",['limit'=>100]);
		
		if(!$list)
			exit;
		
		$title_array = [];
		
		foreach($list as $item)
			$title_array[] = $item->get("title_".$_GET['from']);
		
		
		
		
		$opts = http_build_query(['from'=>$_GET['from'],'to'=>$_GET['to']]);
		
		$serviceurl = "https://serv2.menuturas.lt/services/translate/test.php";
		$serviceurl = "http://vilnele.gw.lt/services/translate/test.php";
		
		$resp = GW_Http_Agent::singleton()->postRequest($serviceurl.'?'.$opts, ['queries'=>json_encode($title_array)]);
		
		$resp = json_decode($resp);
		$count =0;
		
		foreach($list as $idx => $item)
		{
			$item->set("title_{$ln}",$resp[$idx]);
			
			if($this->changed_fields)
				$count++;
			
			$item->updateChanged();
		}
		
		
		$this->setMessage($m="Changed: $count, Speed: ".$t->stop().", Trans sample ".$title_array[0].' -> '.$resp[0].', Request count: '.count($title_array).', Response count: '.count($resp));
		
		
		d::ldump([$m, $title_array, $resp]);
		exit;
		
		
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
