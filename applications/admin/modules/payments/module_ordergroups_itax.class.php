<?php

class Module_OrderGroups_Itax extends GW_Module_Extension
{
	function getOptionsBySellerKey($item, &$options)
	{
		$sellerkey = $item->seller_key;
		
		$options['suplierid'] = $this->config->{"itax_{$sellerkey}_suplier_id"};
		
		
		$productid_conf = json_decode($this->config->{"itax_{$sellerkey}_product_id_conf"}, true);
		
		$type = $item->getType();
		$seller_title = MT_Seller::singleton()->getByIdCached($sellerkey)->title;
		
		$seller_title_kilm = GW_Linksniai_Helper::getLinksnis($seller_title, 'kil');
		
		$description_by_type=[
		    "Touristic"=>"$seller_title_kilm poilsinė kelionė",
		    "Charter"=>"$seller_title_kilm čerteriniai bilietai"
		];
		
		if(!isset($productid_conf[$type]))
		{
			trigger_error("Cant find product id by type($type) in conf $productid_conf", E_USER_ERROR);
		} 
		
		$options['productid'] = $productid_conf[$type];
		$options['departmentid'] = $this->config->{"itax_{$sellerkey}_department_id"};
		$options['taxid'] = $this->config->{"itax_{$sellerkey}_taxid"};
		$options['description'] = $description_by_type[$type] ?? '??';
	}
	
	var $itax = false;
	
	function Itax()
	{
		if($this->itax)
			return $this->itax;
		
		
		$this->config->preload('itax_%');
		$this->itax = new Itax_MT($this->config->itax_mt_endpoint);;	
		
		return $this->itax;
	}
	
	function getClientGroupId($order)
	{
		if($order->company){
			return $this->config->itax_client_juridiniai_group;
		}else{
			return $this->config->itax_client_fiziniai_group;
		}		
	}
	
	
	function doItaxCreateClient($order)
	{
		if($order->company && $order->company != '-'){
			$clientname = $order->company;
		}else{
			$clientname = $order->name ? $order->name." ".$order->surname : $order->user->name.' '.$order->user->surname;
		}
		$email = $order->email ?: $order->user->email;
		
		$itaxclient = new stdClass();		
		
		//$product_id = $this->competition->ext->itax_product_id;
		
		$existingby_code = $order->company_code ? $this->itax->searchClient(['code'=>$order->company_code]) : false;
		if(!isset($existingby_code->count) || $existingby_code->count != 1)
			$existingby_code = false;
		
		
		$search_existing = $existingby_code ? $existingby_code : $this->itax->searchClient(['email'=>$email]);
		
		
		if($existingby_code || (isset($search_existing->response[0]->name) && $search_existing->response[0]->name == $clientname)){
				
			$itaxclient = $search_existing->response[0];
			
			$this->setMessage("Already created ($clientname): <pre>". 
				json_encode($itaxclient, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).'</pre>');
			
			$order->set('extra/itax_client_id', $itaxclient->id);
			$order->set('extra/itax_status_ex/client', 7);
				
			goto sFinish;
		}
		
		if($order->company_code){
			$data['code'] = $order->company_code;
		}
		
		$data['department_id'] = $this->config->itax_default_client_department_id;
		$data['project_id'] = $this->config->itax_default_client_project_id;
		
		$data['client_group_id'] = $this->getClientGroupId($order);
		
		
				
		$data['all_tags'] = array_values(json_decode($this->config->itax_tags_texts, true));
		$data['default_currency'] = $this->config->default_currency_code;
		
		
		if($order->company_addr)
		{
			$data['address'] = $order->company_addr;
		}else{
			$data['address'] = trim($order->city.' '.$order->city.' '.$order->address_l1.' '.$order->address_l2);
		}
		
		if(!$data['address'])
			$data['address'] = 'Vilnius';
		
		
		$itax_replace = ["AA" => "AM"];
		
		$data['country_code'] = $itax_replace[$order->country] ?? $order->country;
		
		if(!$data['country_code'])
			$data['country_code'] = 'LT';		
		
		$data['email'] = $order->email ?: $order->user->email;
		$data['phone'] = $order->phone ?: $order->user->phone;
		
		//$data['code'] = $usercode;
		
		if($order->country)
			$data['vat_business_group_id_by_country_code'] = $order->country;
		
		//d::dumpas([$clientname,$data]);
		
		if(false && GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			$this->setMessage('Add client request[DEV]: <pre>'. json_encode($data, JSON_PRETTY_PRINT).'</pre>');
			//return false;
		}else{
			
			//d::dumpas([$clientname, $data]);
			//production
			$resp = $this->itax->addClient($clientname, $data);
		}
		
		if(isset($resp->response->email) && $resp->response->email == $order->email){
			$this->setMessage('Create success');
			$itaxclient = $resp->response;
			
			//d::dumpas()
			$order->set('extra/itax_client_id', $itaxclient->id);
			$order->set('extra/itax_status_ex/client', 7);
		}else{
			$this->setError('Create failed: <pre>'.json_encode(['url'=>$this->itax->last_url, $resp], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).'</pre>');
			$order->set('extra/itax_status_ex/client', 6);
		}
		
		
		sFinish:
			
		//	d::dumpas(['request'=>$data,'response'=>$itaxclient]);
		//ir nieko cia nebera
			
		return $itaxclient;
	}	
		
		
	
	
	function getOrderDescription($order)
	{
		//$this->initOrderedItems($order);
		
		$text = "";
		$total_qty = 0;
		
		foreach($order->items as $oi){

			
			$text.="{$oi->title} -  {$oi->qty} x {$oi->unit_price} € \n";
			$total_qty+=$oi->qty;
		}
		
		return ['text'=>$text, 'qty'=>$total_qty];
	}


	function getInvoiceData($order, &$info=[])
	{
		$this->itax();
		
		$product_id = $this->config->itax_product_id;
		$department_id = $this->config->itax_default_client_department_id;
		$project_id = $this->config->itax_default_client_project_id;
		$client_id = $order->get('extra/itax_client_id');
		$location_id = $this->config->itax_default_location_id;
	
		$product_title = $this->itax->getOptionTitle('products',$product_id);
		
		$tax_id = $this->config->itax_default_taxid;
		
		//https://www.itax.lt/sales_taxes/{taxid}
		$taxpercent = (int)$this->config->itax_tax_val;
		//$info['taxpercent'] = $taxpercent;
		
		$order_desc = $this->getOrderDescription($order);

		$qty = $order_desc['qty'];
		
		
		$data['postdata']=['invoice'=>[
		    
			"number"=>$info['inv_number'],
			"date" => date('Y-m-d'),
			"client_id" => $client_id,
			"due_date" =>  date('Y-m-d'),
			"currency" => $this->config->default_currency_code,
			'department_id' => $department_id,
			"inv_lines_attributes"=>[[]],
			'all_tags'=> array_values(json_decode($this->config->itax_tags_texts, true)),
			'footer_message'=> $order_desc['text'],
			'external_code' => $order->id,
			'location_id'=>$location_id
		]];		
		
		$il =& $data['postdata']['invoice']['inv_lines_attributes'][0];
		
				
		$il['product_id'] = $product_id;
		$il['total_amount'] = $order->amount_total;

		$il['price_incl_sales_tax'] = $order->amount_total / $qty;

		$il['vat_amount'] = $il['total_amount']/(100+$taxpercent)*$taxpercent;

		$il['amount'] = $il['total_amount'] - $il['vat_amount'];

		$il['price'] = $il['price_incl_sales_tax'] /(100+$taxpercent)*100;	
		$il['qty'] = $qty;
		$il['sales_tax_id']=$tax_id;
		
		
		
		//d::dumpas($data);
		
		//d::Dumpas($data);
/*		
price = 14.82756
price_incl_sales_tax = 16.16204
amount = 3202.75
vat_amount = 288.25
total_amount = 3491.0
*/
		
		
		//d::dumpas($data);
		/*
			$data['total_amount'] = $order->amount_total/$qty;
			$data['unit_price'] = $data['total_amount'] /(100+$taxpercent)*100;
		 */
		
		return $data;
	}		
	
	
	function doItaxWriteInvoice($order)
	{		
		$inv_number = "ENAT ".$order->id;
		
		$res = $this->itax->searchInvoice(['number'=>$inv_number]);
		if(isset($res->response[0]->number) && $res->response[0]->number==$inv_number)
		{
			$this->setMessage("Already created ($inv_number): <pre>". 
				json_encode($res->response[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).'</pre>');
			
			$order->set('extra/itax_invoice_id', $res->response[0]->id);
			$order->set('extra/itax_status_ex/invoice', 7);			

			return $res->response[0];
		}
		//d::dumpas($res);
		$dat=['inv_number'=>$inv_number];
		$data = $this->getInvoiceData($order, $dat);
		
		//d::dumpas
		
		//false && GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV
		
		
		if(isset($_GET['shift_key'])){// debug mode
			$this->setMessage('Add invoice request[DEV]: <pre>'. json_encode($data, JSON_PRETTY_PRINT).'</pre>');
		}
		
		
		if(false){
			$this->setMessage('Add invoice request[DEV]: <pre>'. json_encode($data, JSON_PRETTY_PRINT).'</pre>');
			return false;
		}else{
			//production
			//$this->setMessage('Add invoice request[prod]: <pre>'. json_encode($data, JSON_PRETTY_PRINT).'</pre>');
			$resp = $this->itax->addInvoice($data);			
			
		}
		
		
		if(isset($resp->response->number) && $resp->response->number == $inv_number){
			
			$order->set('extra/itax_invoice_id', $resp->response->id);
			$order->set('extra/itax_status_ex/invoice', 7);
			$this->setMessage('Create invoice success');
			
		}else{
			$order->set('extra/itax_invoice_id', 0);
			$order->set('extra/itax_status_ex/invoice', 6);			
			
			if(isset($_GET['debug'])){
				d::dumpas(json_encode(['url'=>$this->itax->last_url, 'response'=>$resp], JSON_PRETTY_PRINT));
			}
				
			$this->setError('Create invoice failed: <pre>'. json_encode(['url'=>$this->itax->last_url, 'response'=>$resp], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).'</pre>');
		}		
	}	
	
		
	function itaxInsertOrUpdateInvoice($item, $options=[])
	{
		$itax = $this->itax();
		$item->set('errors/itax', new stdClass());
		
		//jei jau bus toks invoice_num mes klaida kad uzimtas
		$item->set('extra/itax_status_ex/invoice', 0);
		
		
		if($item->get('extra/itax_status_ex/purchase')!=7)
			$item->itax_insert_time = date('Y-m-d H:i:s');
		
		
		$price = $options['price'] ?? $item->pay_price;
		$currency = $options['currency'] ?? "EUR";
		$date = $options['date'] ?? $item->insert_time;
		
		$invoice_num = $item->order_id.' - '.$item->advance_price;
		//$footer =  $options['footer'] ?? $this->__buildFooter($item);
				
		
		$search =  $item->get('extra/itax_invoice_id') ? $itax->searchPurchase2(['id'=>$item->get('extra/itax_purchase_id')]) : false;
		$existing = $search->response[0] ?? false;
				
		if($item->get('extra/itax_purchase_id') && !$existing){
			$this->setError("Itax sistemoje nepavyksta rasti iraso, o gal itax.lt nepasiekiama?");
			$this->jump();
		}
		
		$this->getOptionsBySellerKey($item, $options);
		
				
		$footer = "";
			
		//shipping_address
		
		$addPreq=[
			'date' => $item->start_date,
			'due_date' => $item->itax_due_date,
			'suplier_id' => $options['suplierid'],
			'invoice_num'=> $invoice_num,
			'currency' => $item->currency,
			'department_id' => $options['departmentid'],
			'product_id'=> $options['productid'],
			'quantity' => 1,
			'price' => $price,
			//'amount_paid'=>100,
			'currency'=> $currency,
			'unit_price'=>$item->pay_price,
			'tax_id'=>$options['taxid'],
			'all_tags'=>['MTcrmAuto'],
			'footer_message'=> $item->getItaxText('itax_purchase_text').($item->admin_note ? "\n\nPastaba: ".$item->admin_note : ''),
			'discount_amount'=>$item->mt_commissions,
			'description' => $options['description']
		    ];
		
		if($existing){			
			
			if($addPreq['invoice_num'] != $existing->number){
				var_dump($addPreq['invoice_num']);
				var_dump($existing->number);				
				$ERRC="SĄSKAITOS NUMERIO POKYTIS NESAUGOMAS AUTOMATINIU BŪDU-Eug.2019.04.03";
				$this->setMessage(['type'=>GW_MSG_INFO,'text'=>"Sąskaitu numeriai nesutampa crm: {$addPreq['invoice_num']} itax: $existing->number $ERRC"]);
			}
			
			if($addPreq['due_date'] != $existing->due_date){
				var_dump($addPreq['due_date']);
				var_dump($existing->due_date);				
				$ERRC="SĄSKAITOS apmokejimo data POKYTIS NESAUGOMAS AUTOMATINIU BŪDU-Eug.2019.04.08";
				$this->setMessage(['type'=>GW_MSG_INFO,'text'=>"Sąskaitu apmokejimo data nesutampa crm: {$addPreq['due_date']} itax: $existing->due_date $ERRC"]);
			}			
				
			$addPreq['due_date'] = $existing->due_date;
			$addPreq['invoice_num'] = $existing->number;
		}
		
		if($item->get('extra/itax_purchase_id'))
			$addPreq['id'] = $item->get('extra/itax_purchase_id');		
		
		if(isset($options['rename_invoice_num']))
			$addPreq['invoice_num'] = $options['rename_invoice_num'];
				
		
		
		$addPresp = $itax->savePurchase2($addPreq, ['update_if_posted'=>1,'update_if_has_tag'=>'MTcrmAuto']);		
		
		
		if(isset($addPresp->error))
		{
			$item->set('errors/itax/purchase_create_fail', "Purchase create failed");
			$item->set('errors/itax/purchase_details', json_encode(['request'=>$addPreq, 'response'=>$addPresp]));			
			
			$item->set('extra/itax_status_ex/purchase', 6);
			
		}else{
			$item->set('extra/itax_status_ex/purchase', 7);
			
			if(!isset($addPresp->response->id))
			{
				$this->setError('problema su response: <pre>'.$addPresp.'</pre>');
			}
			
			$item->set('extra/itax_purchase_id', $addPresp->response->id);
			
		}
		
		if($item->buyer_id && ($buyer = MT_Passenger::singleton()->find(['id=?', $item->buyer_id])))
		{
			if(!$buyer->isItax()){
				$respo = $this->app->innerRequest("travel/passengers", ["id"=>$buyer->id, "act"=>'doItaxPush', "json"=>1]);
				$buyer->load();
				
				foreach($respo as $notif){
					$notif = (object)$notif;
					if(isset($notif->action) && $notif->action=="notification" && $notif->type == GW_MSG_ERR){
						$item->set('errors/itax/client', $notif->text);
					}
				}
			}
			
			if(!$buyer->isItax())
			{
				$this->setError("Buyer push to itax failed");
				$item->set('extra/itax_status_ex/client', 6);
				$item->set('extra/itax_client_id', 0);
			}else{
				$item->set('extra/itax_status_ex/client', 7);
				$item->set('extra/itax_client_id', $buyer->getItaxId());
			}
		}else{
			$item->set('extra/itax_status_ex/client', 5);
			$item->set('extra/itax_client_id', 0);
		}
		
		if($item->get('extra/itax_client_id') && $item->sell_price){
			//tez departamento id ant itax: 702
			$update = $item->get('extra/itax_invoice_id') > 0;
			
			//$itax_status_ex->invoice=0;
			$addIreq = [];

			GW_Array_Helper::copy($addPreq, $addIreq, ['date','currency','department_id','product_id','quantity','tax_id','all_tags', 'description', 'external_code','due_date']);
						
			$addIreq['footer_message'] = $item->getItaxText('itax_invoice_text');
			$addIreq['price'] = $item->sell_price;
			$addIreq['unit_price'] = $item->sell_price;
			$addIreq['client_id'] = $item->get('extra/itax_client_id');
			
			if($update)
				$addIreq['id'] = $item->get('extra/itax_invoice_id');

			

			$addIresp = $itax->saveInvoice($addIreq, ['update_if_posted'=>1,'update_if_has_tag'=>'MTcrmAuto']);
			

			if($addIresp->response->id)
			{
				$item->set('extra/itax_status_ex/invoice', 7);
				$item->set('extra/itax_invoice_id', $addIresp->response->id);
			}else{
				$item->set('extra/itax_status_ex/invoice', 6);
				
				//jeigu updeitinant erroras tada irgi negerai
				//$item->set('extra/itax_invoice_id', 0);
				
				
				$item->set('errors/itax/invoice_details', json_encode(['request'=>$addIreq, 'response'=>$addIresp, 'update'=>$update?'yes':'no'], JSON_PRETTY_PRINT));
			}
		}else{
			$item->set('extra/itax_status_ex/invoice', 5);
			
			//jeigu updeitinant erroras tada irgi negerai
			//$item->set('extra/itax_invoice_id', 0);
			$item->set('extra/itax_invoice_id', 0);			
		}
		
		
		
		/*
		
		if(isset($addIresp->error))
		{
			$itax_notes[]=['type'=>'error', 'text'=>"Invoice create failed", 'debug'=>['request'=>$addIreq, 'response'=>$addIresp]];
			$itax_status_ex['invoice']=6;
		}else{
			$itax_status_ex['invoice']=7;
		}	
		
		 * 
		 */
		
		sFinish:
			//$item->updateItaxStatus();
			
		if($item->get('errors/itax') == new stdClass)
		{
			$errors = $item->get('errors');
			unset($errors->itax);
			$item->set('errors', $errors);
		}


		$item->updateChanged();
			
		$this->setMessage("#{$item->id}: itax sync status: ".json_encode($item->itax_status_ex));
		
		if(isset($_GET['debug']))
			return false;
	}
		
	function doItaxShowFailDetails()
	{
		$item = $this->getDataObjectById();
		
		$inovice_or_purchase_or_client = $_GET['which'];
		$data = $item->get("errors/itax/{$inovice_or_purchase_or_client}_details");
		$this->setError("<pre>".json_encode(json_decode($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."</pre>");
		$this->jump();
	}


	function doItaxSync($order=false)
	{
		$sys_call = $order ? true: false;
		
		if(!$order)
			$order = $this->getDataObjectById();		
		
		//$item->decompressBody();
		$info = [];
		
		$this->itax();

		
		//if($existing = $this->itax->searchPurchase($pnr, ['single'=>true, 'tagfilter'=>'MTcrmAuto'])){}

		//menuturo stilium
		//$this->itaxInsertOrUpdate($item);
		
		//artistdb stilium
		$itaxclient = $this->doItaxCreateClient($order);
		$invoice = $this->doItaxWriteInvoice($order);
		
		$order->updateChanged();
		
		
		//debug mode
		if(isset($_GET['shift_key']))
			return true;
		
		if(!$sys_call)
			$this->jump();
	}

	function doItaxCancel()
	{
		$item = $this->getDataObjectById();
		
		if($item->get('extra/itax_purchase_id'))
		{
			$response = $this->itax->delete('purchases', $item->get('extra/itax_purchase_id'), ['must_have_tag'=>'MTcrmAuto','bypass_posted'=>true]);
			
			if(isset($response->errcode) && $response->errcode == 601){
				$item->set('extra/itax_status_ex/purchase', 8);
			}
			
			if($response->response->id == $item->get('extra/itax_purchase_id')){
				$item->set('extra/itax_status_ex/purchase', 8);
				$this->setMessage("Pirkimo sąsk pašalinta");
			}else{
				$this->setError( json_encode($response) );
			}
			
			$item->updateChanged();
		}else{
			$this->setError("Nėgaliu pašalinti, jau pašalinta gal?");
		}
		
		$this->jump();
	}
	
	
	function doItaxTest()
	{
		d::dumpas('testOk');
	}
	
	function viewItaxIDs()
	{
		$item = $this->getDataObjectById();
		
		return ['item' => $item];
	}	
	
}