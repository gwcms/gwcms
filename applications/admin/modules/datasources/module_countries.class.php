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
	
	function getOptionsCfg()
	{
		$opts = [
			'title_func'=>function($item){ return $item->get("title_".$this->app->ln).' ('.$item->get('code').')';  },
			'search_fields'=>['code']			
		];	
		
		foreach(GW::s('LANGS') as $ln)
			$opts['search_fields'][]="title_{$ln}";
		
		if(isset($_GET['byCode'])){
			$opts['idx_field'] = 'code';
		}
		
		return $opts;	
	}	
	
	/*
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
	*/
	
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

	function doGetFlags()
	{
		$cnt = 0;
		$list=GW_Country::singleton()->findAll();
		
		$dir = GW::s('DIR/REPOSITORY').'flags/';
		@mkdir($dir, 0777);
		
		if(false)
		foreach($list as $item){
			
			
			$outname = $item->code.'.png';
			$contryname = $item->title_en;
			shell_exec("cd '$dir' && curl 'https://www.countries-ofthe-world.com/flags-normal/flag-of-".$contryname.".png'   -H 'authority: www.countries-ofthe-world.com'   -H 'cache-control: max-age=0'   -H 'upgrade-insecure-requests: 1'   -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36'   -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'   -H 'sec-fetch-site: none'   -H 'sec-fetch-mode: navigate'   -H 'sec-fetch-user: ?1'   -H 'sec-fetch-dest: document'   -H 'accept-language: en-GB,en-US;q=0.9,en;q=0.8,lt-LT;q=0.7,lt;q=0.6,ru;q=0.5'   -H 'cookie: _ga=GA1.2.36452587.1598885137; _gid=GA1.2.1016559131.1598885137'   --compressed > $outname");			
		}
		
		$notfound = [];
		
		foreach(glob("$dir/*.png") as $file){
			if(strpos(file_get_contents($file), "You don't have permission to access this resource.")!==false){
				unlink($file);
				$notfound[] = pathinfo($file, PATHINFO_BASENAME);
			}
		}
		
		d::dumpas(implode(', ', $notfound));
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
