<?php

class Module_Answers_Itax extends GW_Module_Extension
{
	function initItax()
	{
		
		
		$itax = new Itax(GW_Config::singleton()->get('itax/itax_apikey'));		
		$itax->itax_mt = new Itax_MT(GW_Config::singleton()->get('competitions/itax_mt_endpoint'));
		return $itax;
	}
	
		
	
	
	function itaxPurchaseForm($existing, $newvals)
	{
		
		$selajax=['type'=>'select_ajax','options'=>'','preload'=>1];
		$iSel= $selajax + ['optionsview'=>"optionsremote",'modpath'=>'datasources/itax'];
		
		
		
		$form = ['fields'=>[
			'date' => ['type'=>'date', 'required'=>1, 'note'=>'abc'],
			'due_date' => ['type'=>'date', 'required'=>1, 'note'=>'abc'],
			'suplier_id' => $iSel+['source_args' => ['group'=>'supliers']], //is vartotojo $item->set('ext/itax_suplier_id')
			'invoice_num'=> ['type'=>'text', 'default'=>date('Ymd-His')],
			'currency' => ['type'=>'text', 'default'=>'EUR'], 
			'department_id' => $iSel+['source_args' => ['group'=>'departments']], 
			'product_id'=> $iSel+['source_args' => ['group'=>'products']], 
			'quantity' => ['type'=>'number', 'default'=>1],
			'price' => ['type'=>'number'], //is 
			//'amount_paid'=>100,
			//'unit_price'=>['type'=>'number'], 
			'tax_id'=>$iSel+['source_args' => ['group'=>'sales_taxes']], 
			//'all_tags'=>['MTcrmAuto'],
			'tags'=>['type'=>'multiselect_ajax','source_args'=>['group'=>'tags']]+$iSel,
			'footer_message'=> ['type'=>'text'], 
			'discount_amount'=>['type'=>'number'],
			'description' => ['type'=>'text'], 
			'confirm'=>['type'=>'bool', 'required'=>1],
		
		],'cols'=>4];
		
		
		if($existing){
			//$vals['']
			//d::dumpas(['existing'=>$existing,'newvals'=>$newvals]);
			foreach($form['fields'] as $fieldname => $opts){
				//$orig = $existing->$fieldname ?? false;
				if(isset($existing->$fieldname)){
					$form['fields'][$fieldname]['value'] = $existing->$fieldname;
				}
				
				
				
			}
		}
		
		//d::dumpas($form);
		
		foreach($newvals as $fieldname => $newval){
			$valset = $form['fields'][$fieldname]['value'] ?? false;
			if($valset && $valset != $newval){
				$form['fields'][$fieldname]['note'] = "New: <b style='color:red'>".$newval.'</b>';
			}else{
				$form['fields'][$fieldname]['value'] = $newval;
			}
		}
		
		
		
		
		if(!($answers=$this->mod->prompt($form, "ITAX PIRKIMO SĄSKAITA")))
			return false;	
		
		
		foreach($form['fields'] as $fieldname => $cfg){
			if($cfg['type']=='select_ajax' && $answers[$fieldname]=='0')
				unset($answers[$fieldname]);
		}
		
		if($existing){
			$answers['id'] = $existing->id;
		}
		
		unset($answers['confirm']);
		return $answers;
		
				
	}
	
	function doItaxSyncPurchase()
	{
		$itax  = $this->initItax();
		
		$item = $this->getDataObjectById();
		
		
		$existing = [];
		
		
		
		//is prijungiamu kintamuju
		//tapati galima butu panaudoti ir is formos kuri uzpildo pats asmuo
		//tik ne $item->doc->get("keyval/{$groupid}_".$e->fieldname);
		//o $item->get("keyval/".$e->fieldname));
		
		foreach($item->doc->doc_ext_fields as $groupid => $form)
		{
			foreach($form->elements as $e){

				if($e->linkedfields){
					foreach($e->linkedfields as $field){
						list($obj, $key) = explode('/',$field,2);

						if($obj=='itax'){
							$value = $item->doc->get("keyval/{$groupid}_".$e->fieldname);
							//d::ldump([$key, $value]);
							
							
							switch ($key){
								case 'purchase/amount':
									$existing['price'] = $value;
								break;
								case 'purchase/item_title':
									$existing['description'] = $value;
								break;
							}
						}
						
					}
				}
			}			
		}
		
		
		if($item->user->id && $item->user->get("ext/itax_suplier_id")){
			$existing['suplier_id'] = $item->user->get("ext/itax_suplier_id");
		}

		$existing += (array)json_decode($item->get("keyval/purchase_vals"), true);
		
		
		
		
/*		
		//jei jau yra toks tai nekurti naujo
		$search_existing = $itax->searchAny('supliers',['email'=>$item->email]);
		$existing = $search_existing->response[0] ?? false;
		
		$vals=[];
		$vals['name'] = $item->get('ext/contract_fullname');
		$vals['bank_acc'] = $item->get('ext/iban');
		$vals['code'] = $item->get('ext/tax_payer_id');
		$vals['address'] = $item->get('ext/contract_address');
		
		$vals['email'] = $item->get('email');
		$vals['phone'] = $item->get('phone');
		$vals['country_code'] = $item->get('country_code');
		
		if(!$vals['address']){
			$vals['address'] = [];
			if($item->region) $vals['address'][]=$item->region;
			if($item->city) $vals['address'][]=$item->city;
			if($item->address_l1) $vals['address'][]=$item->address_l1;
			if($item->address_l2) $vals['address'][]=$item->address_l2;
			if($item->zipcode) $vals['address'][]=$item->zipcode;
			
			$vals['address'] = implode(', ', $vals['address']);
		}
		
		if(!$vals['name']){
			$vals['name'] = $item->title;
		}
		
		//if(!$existing){
		//	$vals['is_active'] = 1;
		//}
		
		$debug = function($m, $msg) { $m->setMessage("<pre>". (is_array($msg) ? json_encode($msg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $msg).'</pre>');  };
		
*/		//$existing = false;
		$vals = [];
		if(!($answers = $this->itaxPurchaseForm((object)$existing, $vals))){
			return false;
		}
		
		

		
		$item->set("keyval/purchase_vals", json_encode($answers));
		$itax = $this->initItax();
		
		
		$answers['unit_price'] = $answers['price'];
		
		$addPresp = $itax->savePurchase2($answers, ['update_if_posted'=>1,'update_if_has_tag'=>'MTcrmAuto']);
		
		
		if(isset($addPresp->response->id))
		{
			$stat = (array)json_decode($item->get('keyval/itax_status_ex'), true);
			$stat['purchase'] = 7;
			$item->set('keyval/itax_status_ex', json_encode($stat));
			$item->set('keyval/itax_purchase_id', $addPresp->response->id);
			
			$this->setMessage("Itax entry updated");
		}elseif($addPresp->error){
			$this->setError("ITAX Cant create Purchse. <b>{$addPresp->error}</b>: ".json_encode($addPresp->messages));
			
		}else{
			d::dumpas(['vals'=>$answers, 'response'=>$addPresp]);
		}
		
		$this->jump();
		//d::dumpas([$addPresp,$answers]);
		
		/*
		$answers['payment_term']  = $answers['payment_term'] ?? 0;	
		$answers['shipping_days']  = $answers['shipping_days'] ?? 30;		
		

		$response = $itax->insert('supliers','suplier',$answers);
		
		$update = time()-strtotime($response->response->updated_at);
		
		$jsonopt = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
		$this->setMessage("Response: <br/><textarea style='width:100%;height:100px;color:#000'>".json_encode($response, $jsonopt)."</textarea>");
		
		if($response->response){
			if($update < 10){
				$this->setMessage("Itax entry updated");
			}else{
				$this->setMessage("Update Failed? Last update before: ".GW_Math_Helper::uptime($update,2));
			}
		}elseif($response->error){
			
			$debug($this, $answers);			
			
			$this->setError("<b>Itax returned error: </b>".$response->error_description);
			
		}else{
			$debug($this, $itax->last_request_header);
			$debug($this, $itax->last_request_body);
			$this->setError("Unknown situation");
		}
		
		
		
		$item->set('ext/itax_suplier_id', $response->response->id ?? false);
		
		$this->jump();
		 *
		 */
	}	
		
}