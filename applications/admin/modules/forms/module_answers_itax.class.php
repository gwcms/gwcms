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
		
		$selajax=['type'=>'select_ajax','options'=>[],'preload'=>1, 'empty_option'=>1];
		$iSel= $selajax + ['optionsview'=>"optionsremote",'modpath'=>'datasources/itax'];
		
		
		
		$form = ['fields'=>[
			'date' => ['type'=>'date', 'required'=>1, 'note'=>'abc'],
			'due_date' => ['type'=>'date', 'required'=>1, 'note'=>'abc'],
			'suplier_id' => $iSel+['source_args' => ['group'=>'supliers']], //is vartotojo $item->set('ext/itax_suplier_id')
			'invoice_num'=> ['type'=>'text', 'default'=>date('Ymd-His')],
			'currency' => ['type'=>'text', 'default'=>'EUR'], 
			'department_id' => $iSel+['source_args' => ['group'=>'departments']], 
			'product_id'=> $iSel+['source_args' => ['group'=>'products']], 
			'description' => ['type'=>'text','hidden_note'=>'pasirinkite product_id arba jei nera iveskite teksta'],
			'quantity' => ['type'=>'number', 'default'=>1],
			'price' => ['type'=>'number'], //is 
			//'amount_paid'=>100,
			//'unit_price'=>['type'=>'number'], 
			'tax_id'=>$iSel+['source_args' => ['group'=>'sales_taxes']], 
			//'all_tags'=>['MTcrmAuto'],
			'tags'=>['type'=>'multiselect_ajax','source_args'=>['group'=>'tags']]+$iSel,
			'footer_message'=> ['type'=>'text'], 
			'discount_amount'=>['type'=>'number'],
			'journal_balanceable_id' => $iSel+['source_args' => ['group'=>'supliers']], //is vartotojo $item->set('ext/itax_suplier_id')
			'tax_amount' => ['type'=>'number','hidden_note'=>'perduodama i general_journal'], //i
			'confirm'=>['type'=>'bool', 'required'=>1],
			'save2defaults'=>['type'=>'bool', 'required'=>1],
		
		],'cols'=>2];
		
		
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
		
		$defaultkeys = ['department_id','product_id','tax_id','journal_balanceable_id'];
		
		$existing = [];
		$contract_serialnum = '';
		$contract_tax_amount = 0;
		
			
		//is prijungiamu kintamuju
		//tapati galima butu panaudoti ir is formos kuri uzpildo pats asmuo
		//tik ne $item->doc->get("keyval/{$groupid}_".$e->fieldname);
		//o $item->get("keyval/".$e->fieldname));
		$ext_fields = [];
		foreach($item->doc->doc_ext_fields as $groupid => $form)
		{
			foreach($form->elements as $e){
				
				$value = $item->doc->get("keyval/{$groupid}_".$e->fieldname);
				$ext_fields[$groupid][$e->fieldname] = $value;

				if($e->linkedfields){
					foreach($e->linkedfields as $field){
						list($obj, $key) = explode('/',$field,2);
						
						
						if($obj=='itax'){
							
							//d::ldump([$key, $value]);
							
							
							switch ($key){
								case 'purchase/amount':
									$existing['price'] = $value;
								break;
								case 'purchase/item_title':
									$existing['description'] = $value;
								break;
								case 'purchase/serial_num':
									$contract_serialnum = $value;
								break;	
								case 'purchase/tax_amount':
									$contract_tax_amount = $value;
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
		
		
		foreach($defaultkeys as $key){
			if($this->config->{"itax_defaults_".$key})
				$existing[$key] = $this->config->{"itax_defaults_" . $key};
		}		
		
		

		$existing += (array)json_decode($item->get("keyval/purchase_vals"), true);
		
		//$item->set('keyval/act_of_acceptance_date'
		//d::dumpas();
		$existing['date'] = $item->get("keyval/act_of_acceptance_date");
		$existing['due_date'] = date('Y-m-d', strtotime($existing['date'].' +7 DAY'));
		$existing['invoice_num'] = 'Intelektinių paslaugų teikimo sutartis '.$contract_serialnum.'-'.$item->user->id  ;
		$existing['tax_amount'] = $contract_tax_amount;

		$vals = [];
		if(!($answers = $this->itaxPurchaseForm((object)$existing, $vals))){
			return false;
		}
		
		$item->set("keyval/purchase_vals", json_encode($answers));
		$itax = $this->initItax();
		
		
		$answers['unit_price'] = $answers['price'];
		
		if(!$answers['description'] && $answers['product_id']){
			$answers['description'] = $itax->itax_mt->getOptionTitle('products', $answers['product_id']);
		}
		
		
		
		if($answers['save2defaults'] ?? false){
			foreach($defaultkeys as $key){
				$this->config->{"itax_defaults_".$key}  = $answers[$key];
			}
		}
		
		//d::dumpas($answers);
		
		$addPresp = $itax->savePurchase2($answers, ['update_if_posted'=>1,'update_if_has_tag'=>'MTcrmAuto']);
		
		
		if(isset($addPresp->response->id))
		{
			$stat = (array)json_decode($item->get('keyval/itax_status_ex'), true);
			$stat['purchase'] = 7;
			$stat['supplier'] = 7;
			
			
			$item->set('keyval/itax_purchase_id', $addPresp->response->id);
			$item->set('keyval/itax_supplier_id', $answers['suplier_id']);
			
			$this->setMessage("Itax entry updated purchaseId:{$addPresp->response->id} supplierID:{$answers['suplier_id']}");
			
			
			if($answers['journal_balanceable_id'] ?? false){

				$attribs=[	
					'date' => date('Y-m-d'),
					'journable_type' => "Suplier",
					'journable_id' => $answers['suplier_id'],
					'journal_balanceable_type' => "Suplier",
					'journal_balanceable_id' => $answers['journal_balanceable_id'],
					'amount' => $answers['tax_amount'],
					'currency' => "EUR",
					'_destroy' => "false",
					//'id' => "",
					'due_date' => (int)date('d') <= 15 ? date('Y-m-').'15' : date("Y-n-d", strtotime("last day of this month")),
					'reference_number' => "",
					'description' => "",
					'document_number' => ""
				];		

					$data = [];


				$general_joural['number']= "BZ+P".$addPresp->response->id;
				$general_joural['name'] = $item->user->title.' GPM';
				$general_joural['period_closing'] = "";
				$general_joural['fc_closing'] = "";
				$general_joural['department_id'] = "";
				$general_joural['project_id'] = "";
				$general_joural['posted'] = true;
				$general_joural['general_journal_lines_attributes'] = [$attribs];		
				$addJresp = $itax->saveGeneralJournal($general_joural);			

				if($addJresp->response->id ?? false){
					$stat['gjournal'] = 7;
					$item->set('keyval/itax_gjournal_id', $addJresp->response->id);
					$this->setMessage("Itax entry updated general_journalID: ".$addJresp->response->id);
				}
			
			}
			
			$item->set('keyval/itax_status_ex', json_encode($stat));
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