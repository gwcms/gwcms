<?php
class Module_Public_Options extends GW_Module_Extension
{
	
	function viewOptions()
	{
		$i0 = $this->model;
		
		
		$opts = method_exists($this->mod, 'getOptionsCfg') ? $this->getOptionsCfg() : [];
		
		$idx_field = $opts['idx_field'] ?? 'id';
		
		$params = [];
		$cond = "";
		
		if(isset($_GET['q'])){
			$exact = GW_DB::escape($_GET['q']);
			$search = "'%".$exact."%'";

			
			
			if(isset($opts['search_fields'])){
				foreach($opts['search_fields'] as $field){
					$condarr[] = "$field LIKE $search";
					
					
				}
				if($joins=$this->model->findJoinsForFields($opts['search_fields'])){
					$params['joins'] = $joins;
				}
				$simplecond = '('.implode(' OR ', $condarr).')';
				
				//simple cond sample:
				//(team_name LIKE '%%'; OR partic1.name LIKE '%%'; OR partic2.name LIKE '%%'; OR partic1.surname LIKE '%%'; OR partic2.surname LIKE '%%';) AND `event_id`=6
			}else{
				$simplecond = ($opts['search_field'] ?? $this->options_search_field)." LIKE $search";
			}
			
			
			$cond = $opts['condition'] ?? (isset($i0->i18n_fields['title']) ? $i0->buildFieldCond('title',$search) :  $simplecond);
			
		}elseif(isset($_REQUEST['ids'])){
			$ids = json_decode($_REQUEST['ids'], true);
			if(!is_array($ids))
				$ids = [$ids];

			//$ids = array_map('intval', $ids);
			$cond = GW_DB::inConditionStr($idx_field, $ids);
		}	

		if(isset($opts['condition_add'])){
			$cond .= ($cond ? " AND " : ''). $opts['condition_add'];
		}	
				
		$page_by = $opts['page_by'] ?? 30;
		$page = isset($_GET['page']) && $_GET['page'] ? $_GET['page'] - 1 : 0;
		$params['offset'] = $page_by * $page;
		$params['limit'] = $page_by;
		
		if(isset($opts['params_over'])){
			$params = array_merge($params, $opts['params_over']);
		}
	
		
		$list0 = $i0->findAll($cond ?? '', $params);
		
		if(isset($_GET['verbose'])){
			$res['query'] = $i0->getDb()->last_query;
		}
	
				
		if(isset($opts['list_process'])){
			$opts['list_process']($list0);
		}
		
		$list=[];
		
					
		foreach($list0 as $item)
			$list[]=[
			    'id' => $item->get($idx_field), 
			    "title" => isset($opts['title_func']) ? $opts['title_func']($item) : $item->get("title")
			];
		
		//if(isset($_GET['addnew'])){
		//	$list[]=['id'=>'-10','title'=>GW::ln('/g/ADD_NEW_ITEM')];
		//}		
		
		$res['items'] = $list;
		
		
		
		$info = $this->model->lastRequestInfo();
		$res['total_count'] = $info['item_count'];
		
		//if(isset($_GET['addnew'])){
		//	$res['total_count']+=1;
		//}		
		
	
		if(isset($_GET['debug'])){
			header('content-type: text/json');
			echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode($res);
		}
		
		exit;
	}
}