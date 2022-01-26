<?php

class Module_Config_Itax extends GW_Module_Extension
{
	public $itax;
	
	function __initItaxOpts($field)
	{
		$opts = ['cachetime'=>"1 day", 'limit'=>1000];
		
		$map=[
		    'department_id'=>'Departments', 
		    'project_id'=>'projects',
		    'tax_id'=>'SalesTaxes',
		    'client_groups'=>'clientgroups',
		    'location_id'=>'locations'
		];
		
		$result = $this->itax->getOptionsCached($map[$field], $opts);
		
		//d::dumpas($result);
		
		
		$opts = $this->options;
		
		//php 7.2.24 ok BUTTTT php 7.0.33 does not work correctly
		//$opts[$field] = (array)$result->options;
		//so i use json_encode-json_decode that helps		
		$opts[$field] = json_decode(json_encode($result->options), true);
		$this->options = $opts;
		
		
		if(isset($result->cache_updated))
		{
			// (cache time: {$opts['cachetime']})
			$this->setMessage("$field cache was updated");
		}
	}
	
	
	function Itax()
	{
		if($this->itax)
			return $this->itax;
		
		$this->itax = new Itax_MT($this->model->itax_mt_endpoint);;	
		$this->itax->debug = 1;
		
		return $this->itax;
	}	
	
	
	function viewItax()
	{
		
		$this->itax();
	
		//file_put_contents('/tmp/rpc_requests_natos', json_encode($info), FILE_APPEND);

		$this->__initItaxOpts('department_id');
		$this->__initItaxOpts('project_id');
		$this->__initItaxOpts('client_groups');
		$this->__initItaxOpts('tax_id');
		$this->__initItaxOpts('location_id');
		
				
		return $this->viewDefault();
	}
	
	//naujesne versija bus ant artistdb
	//centrine versija ant menuturas.lt/admin/lt/itax
	
	function itaxGetOptions($listname, $opts=[])
	{
		$itax_mt = $this->itax();
		
				
		if(isset($opts['ids']))
		{
			$args['ids'] = $opts['ids'];
		}else{
			if(isset($_GET['q'])){
				$args['search']= "%".GW_DB::escape($_GET['q'])."%";
			}elseif(isset($opts['loadfull'])){
				
				$args['search'] = '%';
			}else{
				$list=[];
				goto sFinish;
			}
			
			$page_by = 30;
			$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
			$args['offset'] = $page_by * $page;
			$args['limit'] = $page_by;		
			$args['listname'] = $listname;
			$args['cachetime'] = '5 MINUTE';			
		}
		
		//d::dumpas([$listname, $args]);
						
		$resp = $itax_mt->getOptionsCached($listname, $args);
				
		
		if(isset($opts['assoc'])){
			foreach($resp->options as $id => $item)
				$list[$id]=$item;			
		}else{
			foreach($resp->options as $id => $item)
				$list[]=['id'=>$id, "title"=>$item];			
		}
		
		sFinish:

		return $list;
	}
	
	function itaxSearchOptions($listname, $opts=[])
	{
		if(isset($_REQUEST['ids']))
			$opts['ids'] = $_REQUEST['ids'];
				
		$res['items'] = $this->itaxGetOptions($listname, $opts);;
		
		if(isset($resp->count))
			$res['total_count'] = $resp->count;
		
		echo json_encode($res);
		exit;		
	}
	
	function viewItaxProductSearch()
	{
		return $this->itaxSearchOptions('products');
	}
	
	function viewItaxTaxSearch()
	{
		return $this->itaxSearchOptions('SalesTaxes', ['loadfull'=>1]);
	}
	
	function viewItaxProjectSearch()
	{
		$itax_mt = new Itax_MT($this->model->itax_mt_endpoint);
		$opts = $itax_mt->getOptions('projects',[]);
		//$opts = [];
		
		
		if(isset($_REQUEST['ids']))
		{
			$ids0 = json_decode($_REQUEST['ids'], true);
			$ids0 = (array)$ids0;
			$ids=[];
			foreach($ids0 as $id)
				$ids[$id]="$id:id - nerasta/nepasiekiama/ištrinta?";
			
			$opts = array_intersect_key($opts, $ids) + $ids;			
		}else{
			$opts = array_filter($opts, function($v, $k) { return mb_stripos($v, $_GET['q'] ?? '')!==false;}, ARRAY_FILTER_USE_BOTH);	
		}
		
		
		
		$opts4ajax = [];
		
		foreach($opts as $id => $title)
			$opts4ajax[] = ['id' => $id, 'title' => $title];
		
		
		
		
		echo json_encode(['items'=>$opts4ajax]);
		
		exit;
	}	
	
	function viewItaxTagSearch()
	{
		return $this->itaxSearchOptions('tags');
	}
	
	function viewItaxSearch()
	{
		$i0 = $this->model;
		
		
		$search = "'%".GW_DB::escape($_GET['q'])."%'";
		$composerid = (int)$_GET['composer_id'];
		
		$cond = "(title_lt LIKE $search OR title_en LIKE $search OR title_ru LIKE $search) AND composer_id=$composerid";
		
		$page_by = 30;
		$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
		$params['offset'] = $page_by * $page;
		$params['limit'] = $page_by;
	
		
		$list0 = $i0->findAll($cond, $params);
		
		$list=[];
		
		foreach($list0 as $item)
			$list[]=['id'=>$item->id, "title"=>$item->get("title_{$this->app->ln}").(!$item->approved ? ' (not approved)' :'')];
		
		$res['items'] = $list;
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
		

		
		echo json_encode($res);
		exit;
	}
	
	function getTaxPercent($taxid)
	{
		$this->itax();
		$this->__initItaxOpts('tax_id');
				
		$taxtitle = $this->options['tax_id'][$taxid] ?? 0;
		$tax_prcnt = (int)str_replace([' ','-','%'],'',$taxtitle);

		//var_dump($taxid);
		//var_dump($this->options['tax_id']);
		//d::Dumpas([ $taxid, $taxtitle, $tax_prcnt, $this->options['tax_id'][(int)$taxid], $this->options['tax_id']["16444"]]);
		
		return $tax_prcnt;
	}
	

	function extEventHandler($event, &$context) 
	{
		switch($event){
			case "BEFORE_SAVE":
				
				
				$vals =& $context;
				
				if(isset($vals['itax_tags'])){
					$tags = $vals['itax_tags'];
 	
					$tags_texts = $this->itaxGetOptions('tags',['ids'=>$tags,'assoc'=>1]);
					$vals['itax_tags_texts'] = json_encode($tags_texts);
				}
				
				if(isset($vals['itax_default_taxid']))
					$vals['itax_tax_val'] = $this->getTaxPercent($vals['itax_default_taxid']);
				
			break;
		}
	}
	
}