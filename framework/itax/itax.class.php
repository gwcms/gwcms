<?php


class Itax 
{
	private $apikey;
	private $end1 = 'https://www.itax.lt/api/v1/';
	private $end2 = 'https://www.itax.lt/api/v2/';
	
	function getEndpoint()
	{
		return ($this->v2 ? $this->end2 : $this->end1);
	}	
	
	public $last_request;
	public $v2=true;
	
	function __construct()
	{
		$this->initConfig();
		$this->apikey = $this->config->itax_apikey_v2;
		$this->v2 = true;
	}
	
	function initConfig()
	{
		$this->config = new GW_Config('itax/');		
		$this->db = GW::db();
	}
	
	
	
	function insert($groupname,$model, $data)
	{
		$url = $this->getEndpoint().$groupname . (isset($data['id'])?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, [$model=>$data]);		
		return $result;
	}
	
	function update($what='purchases', $id, $data)
	{
		$url = $this->getEndpoint().$what . ($id?'/'.$id:'');
		
		//nc -l localhost -p 12345
		//$url = str_replace('https://www.itax.lt','http://localhost:12345', $url);
		
		
		$header=[
				"cache-control: no-cache",
				"content-type: application/json",
			];
		
		if(!$this->v2)
			$header[]='Authorization: Token token="' . $this->apikey . '"';
			
		
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
			CURLOPT_HTTPHEADER => $header,
		]);
		
		if(!$this->v2){
			list($username, $password) = explode('|', $this->apikey);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		}
		
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

		
		if($this->v2){
			list($username, $password) = explode('|', $this->apikey);

			$headers[] = 'Content-Type: application/json; charset=utf-8';
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
		}else{
			$headers[] = 'Authorization: Token token="' . $this->apikey . '"';
		}
		
		
		
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);		

		$rdata = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$header = substr($rdata, 0, $header_size);
		$rdata = substr($rdata, $header_size);

		
		$information = curl_getinfo($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
		$header_info = curl_getinfo($curl,CURLINFO_HEADER_OUT);	
		
		$this->last_request=[
			'headers' => $header_info,
			'body' => $data,
			'response_header'=>$header,
			'response_body'=>$rdata,
			'response_code'=>$httpcode,
		];
		    
		

	
		
		//d::ldump(['request_header'=>$header_info, 'response_header'=>$header, 'response_data'=>$data]);
		
		
		if($data['_DEBUG'] ?? false)
			d::ldump(['request_header'=>$header_info, 'response_header'=>$header, 'request_data'=>$data, 'response_data'=>$rdata]);

		curl_close($curl);
		
		
		//$this->debugRequestResponse();

		return json_decode($rdata);
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
	
	function debugRequestResponse()
	{
		d::dumpas($this->last_request);		
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
		
		
		$url = $this->getEndpoint().'invoices' . (isset($data['id'])?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, $postdata);	
		
		if(isset($_GET['shift_key']))
		{
			$this->debugRequestResponse();
		}
		
		
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
			//'is_paid' => isset($data['is_paid']) && $data['is_paid'],
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
		
		$url = $this->getEndpoint().'purchases' . ($update_id?'/'.$update_id:'');
		
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
		
		
		
		if(isset($data['invoice_lines'])){
			$invoicelines = $data['invoice_lines'];
		}else{
			$invoicelines = [
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
				];
			
			
			//bandymas
			//if(isset($data['tags'])  && $data['tags']){
			//	$invoicelines[0]['tags'] = $data['tags'];
			//}
			
			//d::dumpas(self::__convertTags($data['tags']));
			
			if(isset($data['reverse_vat']) && $data['reverse_vat']=='1'){
				$invoicelines[0]['reverse_vat'] = 1;
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
			"purch_inv_lines_attributes"=>$invoicelines,
			//'is_paid' => isset($data['is_paid']) && $data['is_paid'],// nuo v2 nebeleidzia
			'all_tags'=> isset($data['all_tags']) ? $data['all_tags'] : [],
			'footer_message'=> isset($data['footer_message']) ? $data['footer_message'] : ''
		]];
		
		
		if(isset($data['location_id']))
			$postdata['purchase']["location_id"] = $data['location_id'];		
				
		
		
		$pitm =& $postdata['purchase']['purch_inv_lines_attributes'][0];
		
		//teztour su discount amount eina
		if(isset($data['discount_amount'])){
			$pitm['discount_amount'] = (float)$data['discount_amount'];
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
		
		$url = $this->getEndpoint().'purchases' . (isset($data['id'])?'/'.$data['id']:'');
		
		$result = $this->apiCall(isset($data['id']) ? "PUT" : "POST", $url, $postdata);
		
		
		if($itwasposted){
			$this->update('purchases', $data['id'], ['purchase'=>['posted'=>true]]);
		}				
		
		return $result;
	}
		
	
	function getBussinesGroupIdByCountryCode($cc)
	{
		$eu = explode(',',"BE,EL,LT,PT,BG,ES,LU,RO,CZ,FR,HU,SI,DK,HR,MT,SK,DE,IT,NL,FI,EE,CY,AT,SE,IE,LV,PL,UK");
		if($cc == "LT"){
			$vatid = $this->config->get('itax_vat_business_group_id_lt');
		}elseif(in_array($cc, $eu)){
			$vatid = $this->config->get('itax_vat_business_group_id_eu');
		}else{
			$vatid = $this->config->get('itax_vat_business_group_id_world');
		}

		return $vatid;		
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
			
			$data['vat_business_group_id'] = $this->getBussinesGroupIdByCountryCode($cc);
		}
		
		
		
		$postdata['client']+=$data;
		$client =& $postdata['client'];
		
		if(!isset($data['id'])){
			if(!isset($client['default_currency']))
				$client['default_currency'] = 'EUR';
		}
		
		

		$url = $this->getEndpoint().'clients' . ( isset($data['id']) ?'/'.$data['id']:'');
		$method = isset($data['id']) ? "PUT":"POST";
		$result = $this->apiCall($method, $url, $postdata);		
	
		$result->postdata = $postdata;
		$result->posturl = $url;
		$result->postmethod = $method;
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
		$result = $this->apiCall("GET", $this->getEndpoint().'clients?'. http_build_query($search));
		
		
		return $result;
	}
	
	public $searchPurchase_tagfiltered=0;
	
	function searchPurchase($invoice_num, $options=[])
	{
		$res = $this->apiCall("GET", $this->getEndpoint().'purchases?'. http_build_query(['number'=>$invoice_num]));
		
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
		
		$res = $this->apiCall("GET", $this->getEndpoint().'purchases?'. http_build_query($opts));
				
		return $res;	
	}	
	
	

	
	
	function searchInvoice($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		$result = $this->apiCall("GET", $this->getEndpoint().'invoices'. $q);
		
		return $result;		
	}
	
	function searchInvoiceByClientIDandFooter($client_id,$footer_string)
	{
		$result = $this->apiCall("GET", $this->getEndpoint().'invoices?'. http_build_query(['client_id'=>$client_id]));
		
		foreach($result->response as $res)
		{
			if(strpos($res->footer_message, $footer_string)!==false)
				return $res;
		}
		
		
		return false;	
	}
	
	
	function getClientGroups()
	{
		return $this->apiCall("GET", $this->getEndpoint().'client_groups');
	}
	
	function getDepartments()
	{
		return $this->apiCall("GET", $this->getEndpoint().'departments');
	}
	
	function getProjects($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		
		
		return $this->apiCall("GET", $this->getEndpoint().'projects'.$q);
	}
	
	function getTags()
	{
		return $this->apiCall("GET", $this->getEndpoint().'tags');
	}
	
	function getSupliers()
	{
		$args=[];
		//$args['_DEBUG'] = 1;
		//$args['per_page'] = '2000';
		//$args['page'] = '2';
		
		return $this->apiCall("GET", $this->getEndpoint().'supliers', $args);
	}
	
	function searchAny($groupname, $search=[])
	{		
		$result = $this->apiCall("GET", $this->getEndpoint().$groupname.'?'. http_build_query($search));
		
		return $result;
	}
	
	
	function getSalesTaxes()
	{
		return $this->apiCall("GET", $this->getEndpoint().'sales_taxes');
	}
	
	function getProducts($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		return $this->apiCall("GET", $this->getEndpoint().'products'.$q);
	}
	
	function getAny($name,$opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		if($name=='salestaxes'){
			$name="sales_taxes";
		}
		
		
		return $this->apiCall("GET", $this->getEndpoint().$name.$q);
	}	
	
	
	function getLocations()
	{
		return $this->apiCall("GET", $this->getEndpoint().'locations');
	}
	
	function getClients($opts=[])
	{
		$q = $opts ? '?'.http_build_query($opts):'';
		
		return $this->apiCall("GET", $this->getEndpoint().'clients'.$q);
	}	
	
	

	
	function getOptionsCached($args)
	{
		$last_update = $this->config->get('last_update/'.$args['listname']);
		$resp = [];
		
		$table = 'itax_lists_cache';
		$groupname = $args['listname'];$groupname = strtolower($args['listname']);
		$groupcond = GW_DB::prepare_query(['`group`=?', $groupname]);
		
		
		$renewCached = function() use (&$groupname, &$table, &$groupcond, &$resp)
		{
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
		};
		
		
		if(isset($args['ids']))
		{
			$ids = json_decode($args['ids'], true);
			$ids = (array)$ids;
			
			// TEST IF RENEW---------------------------------------------
			$opts = $this->db->fetch_assoc("SELECT id, name FROM `$table` WHERE ".$groupcond." AND ".GW_DB::inCondition('id', $ids));
			
			$renew = false;
			
			foreach($ids as $id){
				if(!isset($opts[$id]))
					$renew = true;
			}
			
			if($renew)
				$renewCached();
			//END TEST IF RENEW---------------------------------------------
			
			$opts = $this->db->fetch_assoc("SELECT id, name FROM `$table` WHERE ".$groupcond." AND ".GW_DB::inCondition('id', $ids));
			
			foreach($ids as $id){
				if(!isset($opts[$id]))
					$opts[$id] = "$id:id  - nerasta/nepasiekiama/ištrinta?";
			}
			
			return ['options' => $opts];
		}
		
		
		//$resp['minus5min'] = date('Y-m-d H:i:s', strtotime('-'.$args['cachetime']));
		//$resp['lastupdate'] = $last_update;
		
		
		
		if(($args['renew']??false) || date('Y-m-d H:i:s', strtotime('-'.$args['cachetime'])) > $last_update ){
			$renewCached();
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
		
		//if(method_exists($this, "get{$obj}")){
		//	$response = $this->{"get".$obj}();
		
		$response = $this->getAny($obj, ['page'=>1]);
			
		$opts = [];
		foreach($response->response as $row)
			$opts[$row->id] = $row->name;
		
		
		if((int)$response->pagination->pages > 1){
			
			for($page=2;$page<=(int)$response->pagination->pages;$page++){
			
				$response = $this->getAny($obj, ['page'=>$page]);
				
				foreach($response->response as $row)
					$opts[$row->id] = $row->name;	
			
			}
			
		}
		
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
		
		$result = $this->apiCall("GET", $this->getEndpoint().$objType. $q);
		
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
		
		return $this->apiCall("DELETE", $this->getEndpoint().$objType.'/'.$id);
	}
	
	
	function saveGeneralJournal($general_joural)
	{
		
		/*
		 * test 
		$attribs=[	
			'date' => "2023-10-11",
			'journable_type' => "Suplier",
			'journable_id' => "168067",
			'journal_balanceable_type' => "Suplier",
			'journal_balanceable_id' => "30379",
			'amount' => "61",
			'currency' => "EUR",
			'_destroy' => "false",
			//'id' => "",
			'due_date' => "2023-10-15",
			'reference_number' => "",
			'description' => "",
			'document_number' => ""
		];		

			$data = [];


		$general_joural['number']= "BZ+201111111";
		$general_joural['name'] = "vardas+pabarde+GPM";
		$general_joural['period_closing'] = "";
		$general_joural['fc_closing'] = "";
		$general_joural['department_id'] = "";
		$general_joural['project_id'] = "";
		$general_joural['posted'] = true;
		$general_joural['general_journal_lines_attributes'] = [$attribs];
		 
		 */

		$data['general_journal'] = $general_joural;
		$data['_DEBUG'] = 1;
		
		//d::ldump([$data ,$general_joural]);
		
		
		$url = $this->getEndpoint().'general_journals' . ( isset($data['id']) ?'/'.$data['id']:'');
		$result = $this->apiCall(isset($data['id']) ? "PUT":"POST", $url, $data);
		
		//d::dumpas(['result'=>$result]);
		
	
		$result->postdata = $data;
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
	
	
	function getTagNames($ids)
	{
		$resp = Menuturas_Api::singleton()->request('itax/itax/optionsajax', ['group'=>'tags', 'ids'=>json_encode($ids)], [], []);
		
		$resp = json_decode($resp, true);
		
		$rez = [];
		foreach((array)$resp['items'] as $row)
			$rez[] = $row['title'];
		
		return $rez;
	}
}
