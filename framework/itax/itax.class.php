<?php


class Itax 
{
	private $apikey;
	const ENDPOINT = 'https://www.itax.lt/api/v1/';
	public $last_request_header;
	public $last_request_body;
	
	function __construct($apikey)
	{
		$this->apikey = $apikey;
	}
	
	function initConfig()
	{
		$this->config = new GW_Config('itax/');		
		$this->db = GW::db();
	}
	
	
	
	function insert($groupname,$model, $data)
	{
		$url = self::ENDPOINT.$groupname . (isset($data['id'])?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, [$model=>$data]);		
		return $result;
	}
	
	function update($what='purchases', $id, $data)
	{
		$url = self::ENDPOINT.$what . ($id?'/'.$id:'');
		
		//nc -l localhost -p 12345
		//$url = str_replace('https://www.itax.lt','http://localhost:12345', $url);
		
		
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 3,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLINFO_HEADER_OUT => true,
			CURLOPT_HTTPHEADER => [
				'Authorization: Token token="' . $this->apikey . '"',
				"cache-control: no-cache",
				"content-type: application/json",
			],
		]);
		
		$data = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header = substr($data, 0, $header_size);
		$data = substr($data, $header_size);

		$this->last_request_header = $header;
		$this->last_request_body = $data;
		$information = curl_getinfo($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		
		$header_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);
		
		//d::ldump(['request_header'=>$header_info, 'response_header'=>$header, 'response_data'=>$data]);	
		
		curl_close($curl);
		return json_decode($data);	
		
	}
	
	function apiCall($method, $url, $data = false)
	{
		$curl = curl_init();

		switch ($method) {
			case "POST":
				curl_setopt($curl, CURLOPT_POST, 1);

				if ($data)
				//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			break;
			case "PUT":
				//curl_setopt($curl, CURLOPT_PUT, 1); //cant postfields with this
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				
				if ($data)
				//curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
			break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			break;	
			default:
				if ($data)
					$url =  $url.'?'.http_build_query($data);
		}

		// Optional Authentication:
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$headers[] = 'Authorization: Token token="' . $this->apikey . '"';
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);		

		$data = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header = substr($data, 0, $header_size);
		$data = substr($data, $header_size);

		$this->last_request_header = $header;
		$this->last_request_body = $data;
		$information = curl_getinfo($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		

		$header_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);		
		
		//d::ldump(['request_header'=>$header_info, 'response_header'=>$header, 'response_data'=>$data]);


		curl_close($curl);

		return json_decode($data);
	}
	
	
	function prepareInvoicePostData($data, $opts)
	{
		$postdata = ['invoice'=>[
			"date" => $data['date'],
			"client_id" => $data['client_id'],
			"due_date" => isset($data['due_date']) ? $data['due_date'] : $data['date'],
			"currency" => $data['currency'],
			'department_id' => $data['department_id'],
			"inv_lines_attributes"=>[
				[
					"product_id"=>$data['product_id'],
					"price" =>  $data['unit_price'],
					"qty" => $data['quantity'],
					"sales_tax_id" => $data['tax_id'],
					"vat_amount" => isset($data['vat_amount']) ? $data['vat_amount'] : 0,
					"total_amount" => $data['total_amount'] ?? $data['price'],		
				]
			],
			'all_tags'=> isset($data['all_tags']) ? $data['all_tags'] : [],
			'footer_message'=> isset($data['footer_message']) ? $data['footer_message'] : '',
			'external_code' => $data['external_code'] ?? ''
		]];
		
		if(isset($data['invoice_num']))
			$postdata['invoice']["number"] = $data['invoice_num'];
		
		$invitem =& $postdata['invoice']['inv_lines_attributes'][0];
		
		
		if(isset($data['location_id']))
			$postdata['invoice']["location_id"] = $data['location_id'];		
		
		if(isset($data['price']) && !isset($data['amount'])){
			$invitem["amount"] = $data['price'];
		}elseif(isset($data['amount'])){
			$invitem["amount"] = $data['amount'];
		}
		
		if(isset($data['price_incl_sales_tax']))
		{
			$invitem['price_incl_sales_tax'] = $data['price_incl_sales_tax'];
			//$invitem['total_amount'] = $data['price_incl_sales_tax'];
			
			if(!isset($data['not_vat_aggregate'] )){
				unset($invitem['total_amount']);

				$invitem['vat_aggregate'] = 1;
				unset($invitem['vat_amount']);
				unset($invitem['price']);				
			}
		}	
		
		if($data['id'] ?? false && $existing->inv_lines[0]){
			$invitem['id'] = $existing->inv_lines[0]->id;
		}	
		
		if(isset($data['project_id'])){
			$postdata['invoice']['project_id'] = $data['project_id'];
		}
		return $postdata;
	}
	
	function saveInvoice($data, $opts=[])
	{
		$itwasposted = false;
		
		
		if($data['id'] ?? false)
		{
			$search = $this->searchInvoice(['id'=>$data['id']]);
			$existing = $search->response[0];
			
			
			
			if(!$existing || ($existing->id !=$data['id']))
			{
				return ['error'=>"Cant update invoice({$data['id']}) Maybe it is deleted OR network problem OR api changed"];
			}
			
			if(isset($opts['update_if_has_tag']))
			{
				$tags = self::__convertTags($existing->tags);
				
				if(!isset($tags[$opts['update_if_has_tag']])){
					return ['error'=>"Cant update invoice({$data['id']}) Required tag {$opts['update_if_has_tag']} not present"];
				}
			}
			
			if($existing->posted){
				if(!isset($opts['update_if_posted'])){
					return ['error'=>"Cant update invoice({$data['id']}) It is already posted, add to opts update_if_posted=>1"];
				}else{
					//unpost
					$itwasposted = true;
					$this->update('invoices', $data['id'], ['invoice'=>['posted'=>false]]);
				}
			}			
		}
		
		if(isset($data['postdata'])){
			$postdata = $data['postdata'];
		}else{
			$postdata = $this->prepareInvoicePostData($data, $opts);
		}
		
		
		$url = self::ENDPOINT.'invoices' . (isset($data['id'])?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, $postdata);	
		
		
		if($itwasposted){
			$this->update('invoices', $data['id'], ['invoice'=>['posted'=>true]]);
		}		
		
		
		if(isset($result->error) && $result->error){
			//nedaryti tokiu bajeriu, servizas cia gi irgi
			//d::dumpas([$postdata, $existing]);
			$result->postdata = $postdata;
			if(isset($existing) && $existing)
				$result->existing = $existing;
		}
		
		file_put_contents(GW::s('DIR/LOGS').'itax_invoice_post.log', json_encode($postdata+['time'=>date('Y-m-d H:i:s')], JSON_PRETTY_PRINT), FILE_APPEND);
		
		return $result;
	}
	
	function savePurchase($data, $update_id=false)
	{
		//veikia su ryanair ir wizzair
		$postdata = ['purchase'=>[
			"date"=>$data['date'],
			//"client_id"=>$ryanair_client_id,
			"suplier_id" => $data['suplier_id'],
			"number" => $data['invoice_num'],
			"due_date" => isset($data['due_date']) ? $data['due_date'] : $data['date'],
			"currency" => $data['currency'],
			'department_id' => $data['department_id'],
			"purch_inv_lines_attributes"=>[
				[
					"product_id" => $data['product_id'],
					"price" => $data['unit_price'],
					"qty" => $data['quantity'],
					"amount" => $data['price'],
					"sales_tax_id" => $data['tax_id'],
					"vat_amount" => $data['vat_amount'] ?? 0,
					"total_amount" => $data['price'],
					"description" => $data['description']
				]
			],
			'is_paid' => isset($data['is_paid']) && $data['is_paid'],
			'all_tags'=> isset($data['all_tags']) ? $data['all_tags'] : [],
			'footer_message'=> isset($data['footer_message']) ? $data['footer_message'] : ''
		]];
		
		$pitm =& $postdata['purchase']['purch_inv_lines_attributes'][0];
		
		//teztour su discount amount eina
		if(isset($data['discount_amount'])){
			$pitm['discount_amount'] = $data['discount_amount'];
			$pitm['amount'] -= $pitm['discount_amount'];
			$pitm['total_amount'] = $pitm['amount'] + $pitm['vat_amount'];
		}
		
		if(isset($data['amount_paid']))
			$postdata['amount_paid'] = $data['amount_paid'];
		
		if(isset($data['purch_inv_lines_attributes_id']))
			$pitm['id'] = $data['purch_inv_lines_attributes_id'];
		
		if(isset($data['external_code']))
			$postdata['purchase']['external_code'] = $data['external_code'];		
		
		
		//tomas a. : nera tokio featuro
		//if(isset($data['file']))
		//	$postdata['file']= base64_encode($data['file']);
		
		//if($update_id)
		//	$postdata['purchase']['id']=$update_id;

		//unset($postdata['purchase']['purch_inv_lines_attributes']);
		
		//d::dumpas($postdata);
		
		$url = self::ENDPOINT.'purchases' . ($update_id?'/'.$update_id:'');
		
		$result = $this->apiCall($update_id ? "PUT" : "POST", $url, $postdata);
		
		
		return $result;
	}
	
	
	function savePurchase2($data, $opts=[])
	{
		$itwasposted = false;
		
		if(isset($opts['update_by_inv_num']))
		{
			$search = $this->searchPurchase2(['number'=>$data['invoice_num']]);
			$existing = $search->response[0];
			
			$data['id']=$existing->id;
		}		
		
		if(isset($data['id']) && $data['id'])
		{
			$search = $this->searchPurchase2(['id'=>$data['id']]);
			$existing = $search->response[0];
			
			$data['purch_inv_lines_attributes_id'] = $existing->purch_inv_lines[0]->id;
			
			if(!$existing || ($existing->id !=$data['id']))
			{
				return ['error'=>"Cant update purchase({$data['id']}) Maybe it is deleted OR network problem OR api changed"];
			}
			
			if(isset($opts['update_if_has_tag']))
			{
				$tags = self::__convertTags($existing->tags);
				
				if(!isset($tags[$opts['update_if_has_tag']])){
					return ['error'=>"Cant update purchase({$data['id']}) Required tag {$opts['update_if_has_tag']} not present"];
				}
			}
			
			if($existing->posted){
				if(!isset($opts['update_if_posted'])){
					return ['error'=>"Cant update purchase({$data['id']}) It is already posted, add to opts update_if_posted=>1"];
				}else{
					//unpost
					$itwasposted = true;
					$this->update('purchases', $data['id'], ['purchase'=>['posted'=>false]]);
				}
			}		
		}
		
		//veikia su ryanair ir wizzair
		$postdata = ['purchase'=>[
			"date"=>$data['date'],
			//"client_id"=>$ryanair_client_id,
			"suplier_id" => $data['suplier_id'],
			"number" => $data['invoice_num'],
			"due_date" => isset($data['due_date']) ? $data['due_date'] : $data['date'],
			"currency" => $data['currency'],
			'department_id' => $data['department_id'],
			"purch_inv_lines_attributes"=>[
				[
					"product_id" => $data['product_id'],
					"price" => $data['unit_price'],
					"qty" => $data['quantity'],
					"amount" => $data['price'],
					"sales_tax_id" => $data['tax_id'],
					"vat_amount" => $data['vat_amount'] ?? 0,
					"total_amount" => $data['price'],
					"description" => $data['description']
				]
			],
			'is_paid' => isset($data['is_paid']) && $data['is_paid'],
			'all_tags'=> isset($data['all_tags']) ? $data['all_tags'] : [],
			'footer_message'=> isset($data['footer_message']) ? $data['footer_message'] : ''
		]];
		
		$pitm =& $postdata['purchase']['purch_inv_lines_attributes'][0];
		
		//teztour su discount amount eina
		if(isset($data['discount_amount'])){
			$pitm['discount_amount'] = $data['discount_amount'];
			$pitm['amount'] -= $pitm['discount_amount'];
			$pitm['total_amount'] = $pitm['amount'] + $pitm['vat_amount'];
		}
		
		if(isset($data['amount_paid']))
			$postdata['amount_paid'] = $data['amount_paid'];
		
		if(isset($data['purch_inv_lines_attributes_id']))
			$pitm['id'] = $data['purch_inv_lines_attributes_id'];
		
		if(isset($data['external_code']))
			$postdata['purchase']['external_code'] = $data['external_code'];		
		
		
		//tomas a. : nera tokio featuro
		//if(isset($data['file']))
		//	$postdata['file']= base64_encode($data['file']);
		
		//if($update_id)
		//	$postdata['purchase']['id']=$update_id;

		//unset($postdata['purchase']['purch_inv_lines_attributes']);
		
		//d::dumpas($postdata);
		
		$url = self::ENDPOINT.'purchases' . (isset($data['id'])?'/'.$data['id']:'');
		
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, $postdata);
		
		
		if($itwasposted){
			$this->update('purchases', $data['id'], ['purchase'=>['posted'=>true]]);
		}				
		
		return $result;
	}
		
	
	
	function saveClient($name, $data=[])
	{
		
		$postdata = [];
		$postdata['client']['name']=$name;
		$postdata['client']['payment_term']= $data['payment_term'] ?? '30';
		
		
		//$postdata['client']['all_tags'] = isset($data['all_tags']) ? $data['all_tags'] : [];
		
		
		if(isset($data['vat_business_group_id_by_country_code']))
		{
			$cc = $data['vat_business_group_id_by_country_code'];
			unset($data['vat_business_group_id_by_country_code']);
			
			$eu = explode(',',"BE,EL,LT,PT,BG,ES,LU,RO,CZ,FR,HU,SI,DK,HR,MT,SK,DE,IT,NL,FI,EE,CY,AT,SE,IE,LV,PL,UK");
			if($cc == "LT"){
				$vatid = $this->config->get('itax_vat_business_group_id_lt');
			}elseif(in_array($cc, $eu)){
				$vatid = $this->config->get('itax_vat_business_group_id_eu');
			}else{
				$vatid = $this->config->get('itax_vat_business_group_id_world');
			}
				
			$data['vat_business_group_id'] = $vatid;
		}		
		
		
		
		$postdata['client']+=$data;
		$client =& $postdata['client'];
		
		if(!isset($data['id'])){
			if(!isset($client['default_currency']))
				$client['default_currency'] = 'EUR';
		}
		
		

		$url = self::ENDPOINT.'clients' . ( isset($data['id']) ?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT":"POST", $url, $postdata);		
	
		$result->postdata = $postdata;
		/*
		POST https://www.itax.lt/api/v1/clients
		{  
		   "client":{  
		      "name":"Testas",
		      "default_currency":"EUR",
		      "payment_term":30,
		      "all_tags":["Svarbu", "GPM"]
		   }
		}
		 */	
		return $result;
	}
	
	function searchClient($search=[])
	{		
		$result = $this->apiCall("GET", self::ENDPOINT.'clients?'. http_build_query($search));
		
		
		return $result;
	}
	
	public $searchPurchase_tagfiltered=0;
	
	function searchPurchase($invoice_num, $options=[])
	{
		$res = $this->apiCall("GET", self::ENDPOINT.'purchases?'. http_build_query(['number'=>$invoice_num]));
		
		$this->searchPurchase_tagfiltered=0;
		
		if($res->count && isset($options['tagfilter'])){
			$newlist = [];
			$init_count = $res->count;
			
			foreach($res->response as $idx => $row)
			{
				$tags = self::__convertTags($row->tags);
				
				if(isset($tags[$options['tagfilter']])){
					$newlist[]=$row;
				}
			}
			
			$res->response = $newlist;
			$res->count = count($newlist);
			$this->searchPurchase_tagfiltered = $init_count-$res->count;
		}
		
		
		if($options['single']){
			if($res->count==1 && $res->response[0]->number==$invoice_num){
				
				return $res->response[0];
			}else{
				return false;
			}
		}else{
			return $res;		
		}		
	}
	
	function searchPurchase2($opts=[])
	{
		//pagai invoice numeri -- ['number'=>$invoice_num];
		
		$res = $this->apiCall("GET", self::ENDPOINT.'purchases?'. http_build_query($opts));
				
		return $res;	
	}	
	
	
	function searchInvoice($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		$result = $this->apiCall("GET", self::ENDPOINT.'invoices'. $q);
		
		return $result;		
	}
	
	function searchInvoiceByClientIDandFooter($client_id,$footer_string)
	{
		$result = $this->apiCall("GET", self::ENDPOINT.'invoices?'. http_build_query(['client_id'=>$client_id]));
		
		foreach($result->response as $res)
		{
			if(strpos($res->footer_message, $footer_string)!==false)
				return $res;
		}
		
		
		return false;	
	}
	
	
	function getClientGroups()
	{
		return $this->apiCall("GET", self::ENDPOINT.'client_groups');
	}
	
	function getDepartments()
	{
		return $this->apiCall("GET", self::ENDPOINT.'departments');
	}
	
	function getProjects($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		
		
		return $this->apiCall("GET", self::ENDPOINT.'projects'.$q);
	}
	
	function getTags()
	{
		return $this->apiCall("GET", self::ENDPOINT.'tags');
	}
	
	function getSupliers()
	{
		return $this->apiCall("GET", self::ENDPOINT.'supliers');
	}
	
	function searchAny($groupname, $search=[])
	{		
		$result = $this->apiCall("GET", self::ENDPOINT.$groupname.'?'. http_build_query($search));
		
		return $result;
	}
	
	
	function getSalesTaxes()
	{
		return $this->apiCall("GET", self::ENDPOINT.'sales_taxes');
	}
	
	function getProducts($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		return $this->apiCall("GET", self::ENDPOINT.'products'.$q);
	}
	
	function getAny($name,$opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		return $this->apiCall("GET", self::ENDPOINT.$name.$q);
	}	
	
	
	function getLocations()
	{
		return $this->apiCall("GET", self::ENDPOINT.'locations');
	}
	
	function getClients($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		return $this->apiCall("GET", self::ENDPOINT.'clients'.$q);
	}	
	
	
	function getOptionsCached($args)
	{
		$last_update = $this->config->get('last_update/'.$args['listname']);
		$resp = [];
		
		$table = 'itax_lists_cache';
		$groupname = $args['listname'];$groupname = $args['listname'];
		$groupcond = GW_DB::prepare_query(['`group`=?', $groupname]);
		
		
		if(isset($args['ids']))
		{
			$ids = json_decode($args['ids'], true);
			$ids = (array)$ids;
			
			$opts = $this->db->fetch_assoc("SELECT id, name FROM `$table` WHERE ".$groupcond." AND ".GW_DB::inCondition('id', $ids));
			
			foreach($ids as $id){
				if(!isset($opts[$id]))
					$opts[$id] = "$id:id  - nerasta/nepasiekiama/ištrinta?";
			}
			
			return ['options' => $opts];
		}
		
		
		//$resp['minus5min'] = date('Y-m-d H:i:s', strtotime('-'.$args['cachetime']));
		//$resp['lastupdate'] = $last_update;
		
		
		
		if(date('Y-m-d H:i:s', strtotime('-'.$args['cachetime'])) > $last_update ){
		
			
			//return $table;

			$options = $this->getOptions($groupname);
			$this->db->query("DELETE FROM `$table` WHERE $groupcond");


			$rows = [];

			foreach($options as $id => $name)
			{
				$row=[];
				$row['id']=$id;
				$row['name']=$name;
				$row['group']=$groupname;
				$rows[] = $row;
			}

			$this->db->multi_insert($table,$rows);

			
			if($rows){//jei nieko negauta tai neisaugot last_update
				$this->config->set('last_update/'.$args['listname'], date('Y-m-d H:i:s'));
			}
			
			$resp['cache_updated'] = 1;
		}
		
		
		
		$offset = (int)($args['ofset'] ?? 0);
		$limit = (int)($args['limit'] ?? 30);
		
		$cond = isset($args['search']) ? GW_DB::prepare_query(["`name` LIKE ?", $args['search']]) : '1=1';
		$cond.= " AND ".$groupcond;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS id, name FROM `$table` WHERE " .$cond." LIMIT $offset, $limit";
		$resp['options'] = $this->db->fetch_assoc($sql);
		
		$resp['args'] = $args;
		
		
		$resp['last_update'] = $last_update;
		$resp['offset'] = $offset;
		$resp['limit'] = $limit;
		$resp['count'] = $this->db->fetch_result("SELECT FOUND_ROWS()");
		
		return $resp;
		
		
		
	}
	
		
	


	function getOptions($obj)
	{
		
		if(method_exists($this, "get{$obj}")){
			$response = $this->{"get".$obj}();
		}else{
			$response = $this->getAny($obj);
		}
		
		$opts = [];
		foreach($response->response as $row)
			$opts[$row->id] = $row->name;
		
		return $opts;
	}
	
	static function __convertTags($tags)
	{
		$new = [];
		foreach($tags as $tag)
			$new[$tag->name]=1;
		return $new;
	}
	
	
	
		//$clientname =  $item->client;
		
		/*
		 * is kontrolerio panaudojimo pavyzdys sukurt klienta
		 * INVOICE KAI NEBEREIKIA ir kliento kurimo nereikia
		$res = $itax->searchClient($clientname);
		
		if($res->count == 1 || $res->count > 1)
		{			
			if($res->count > 1){
				$itax_notes[]=['type'=>'notice', 'text'=>'Search by client name "'.$clientname.'" gave more than one resut. Result count: '.$res->count];
				$itax_status_ex['client_id']=5;
			}else{
				$itax_status_ex['client_id']=7;
			}
			
			
			$client = $res->response[0];
			$clientid = $client->id;
		}else{
			$addresp = $itax->saveClient($clientname,['default_currency'=>'EUR', 'all_tags'=>['RyanairAuto']]);
			
			if($addresp->response->name == $clientname)
			{
				$clientid = $addresp->response->id;
				
				$itax_notes[]=['type'=>'success', 'text'=>"Client create succeed"];
				
				$itax_status_ex['client_id']=7;
			}else{
				$itax_notes[]=['type'=>'error', 'text'=>"Client create failed", 'debug'=>$addresp];
				
				$item->itax_status = 6;
				$itax_status_ex['client_id']=6;
				
				GOTO sFinish;
			}
		}
		*/	
	
	
	
	function search($objType, $opts=[])
	{
		
		$q = $opts ? '?'.http_build_query($opts):'';
		
		$result = $this->apiCall("GET", self::ENDPOINT.$objType. $q);
		
		//d::dumpas($result);
		
		return $result;	
	}
	
	
	public $objTrans = ['purchases'=>'purchase', 'invoices'=>'invoice', 'clients'=>'client'];
		
	function getObjSinglForm($objType)
	{
		return $this->objTrans[$objType] ?? $objType;		
	}
	
	function delete($objType, $id, $opts=[])
	{
		if(isset($opts)){
			$search = $this->search($objType, ['id'=>$id]);
			$existing = $search->response[0];
						
			
			
			if(!$existing || ($existing->id != $id))
			{
				return (object)['error'=>"Cant delete $objType({$id}) Maybe it is already deleted OR network problem OR api changed", 'errcode'=>601];
			}
			
			if(isset($opts['must_have_tag']))
			{
				$tags = self::__convertTags($existing->tags);
				
				if(!isset($tags[$opts['must_have_tag']])){
					return (object)['error'=>"Cant delete $objType({$id}) Required tag {$opts['must_have_tag']} not present check must_have_tag option"];
				}
			}

			if($existing->posted){
				if(!isset($opts['bypass_posted'])){
					return (object)['error'=>"Cant update $objType({$id}) It is posted, add to opts bypass_posted=>1"];
				}else{
					//unpost
					$itwasposted = true;
					$this->update($objType, $id, [$this->getObjSinglForm($objType)=>['posted'=>false]]);
				}
			}
			
		}
		
		return $this->apiCall("DELETE", self::ENDPOINT.$objType.'/'.$id);
	}
	
}