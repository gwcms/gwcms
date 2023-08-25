<?php


class Module_Docs extends GW_Public_Module
{	

	function init()
	{
		$this->model = GW_Doc::singleton();
		$this->app->carry_params['obj'] = 1;
		$this->app->carry_params['multiple']=1;	
		$this->modconfig = $this->initModCfgEx("docs");
	}
	
	
	function getDataObjectById($load=true, $class=false, $access=GW_PERM_READ) 
	{
		$id = $this->getCurrentItemId();
		
		if (!$id){
			$this->setError('/g/GENERAL/BAD_ARGUMENTS');
			$this->app->jump('');
		}
		
		
		$item = $this->model->find(['`key`=?',$id]);
		
		if (!$item){
			$this->setError('/g/GENERAL/ITEM_NOT_EXISTS');
			$this->app->jump('');
		}
		
		return $item;
	}
	
	
	function getStepIdx($step)
	{
		return array_search($step , $this->steps);		
	}
	
	public $steps = [];
	function initSteps()
	{
			
		if(!($_GET['s'] ?? false)){

			$_GET['s'] = 1;
		}
		
		//get steps
		if($this->steps)
			return true; //second call
		
		
		if(!$this->modconfig->steps)
		{
			d::dumpas('NOT CONFIGURED PLEASE ADD CONFIGURATION FROM ADMIN');
		}
				
		$this->steps = json_decode($this->modconfig->steps, true);
		
		

				
		
		//if($_SERVER['REMOTE_ADDR']=='78.61.228.118')
		//	d::dumpas($this->register_steps);
				
				
		if(is_numeric($_GET['s'])){
			$this->steps_current = $this->steps[$_GET['s']-1];
		}elseif(in_array($_GET['s'], $this->steps) || $_GET['s']=='finish'){
			$this->steps_current = $_GET['s'];
		}
		
		$curridx = $this->getStepIdx($this->steps_current);
			
		
		
		$this->steps_prev = $this->steps[$curridx-1] ?? false;
		
		$this->steps_next = $this->steps[$curridx+1] ?? false;
				
		
	}
		
	function isSigned($answer)
	{
		if($answer->signature || $answer->keyval->signed_document_filename){
			return true;
		}
	}
	
	
	function viewItem()
	{
		
		$this->initSteps();
		$this->userRequired();
		
		$item = $this->getDataObjectById();
		
		
		//sukurt tuscia atsakyma su sasaja, kad iskart galetu sutarties blank variante $answer->obj panaudot
		if(isset($_GET['obj'])){
			$this->prepareNewAnswer($item);
		}
		
		foreach($item->doc_forms as $groupid => $form){
			
			$answids = $item->get("keyval/vars_{$groupid}");
			
			if($answids==false){
				//nenurodyta
				$answids=[];
			}elseif(is_numeric($answids)){
				//senasis variantas atsakymas vienas
				$answids = [$answids];
			}else{
				//naujasis kai daugiau nei vienas arba vienas
				$answids = json_decode($answids, true);
			}			
			
			$answ = [];
			
			foreach($answids as $answid)
				$answ[] = GW_Form_Answers::singleton()->createNewObject($answid, true);
			
			
			if(count($answ)==1){
				//senasis variantas atsakymas vienas arba naujasis kai atsakymas tik vienas parinktas
				foreach($form->elements as $fieldname => $e)
					$this->tpl_vars["vars_".$groupid][$fieldname] = $answ[0]->get("keyval/$fieldname");
			}else{
				//naujasis kai daugiau nei vienas
				foreach($answ as $answidx => $x)
					foreach($form->elements as $fieldname => $e)
						$this->tpl_vars["vars_".$groupid][$answidx][$fieldname] = $answ[$answidx]->get("keyval/$fieldname");			
			}
		}
		foreach($item->doc_ext_fields as $groupid => $form){
			
				
			
			foreach($form->elements as $fieldname => $e){
				if($e->i18n)
					$fieldname="{$fieldname}_{$this->app->ln}";
					
				
				//d::ldump(["keyval/{$groupid}_{$fieldname}", $item->get("keyval/{$groupid}_{$fieldname}")]);
				$val = $item->get("keyval/{$groupid}_{$fieldname}");
				$this->tpl_vars["ext_fields_".$groupid][$fieldname] = $val;
				
				//d::ldump([$e,"ext_fields_{$groupid}/$fieldname",$this->tpl_vars["ext_fields_".$groupid][$fieldname]]);
			}
		}	
		
		$vals = [];
		

		
		
		/*
{foreach $item->doc_ext_fields as $groupid => $form}
{foreach $form->elements as $fieldname => $input}
{if $input->get(i18n)}{$i18n_suff="_{$app->ln}"}{else}{$i18n_suff=""}{/if}
{$var="ext_fields_{$groupid}.{$fieldname}{$i18n_suff}"}
{if $input->type == 'date'}		
		*/
		$tploptions=[];
		$this->tpl_vars['item'] = $item;
		$this->app->page->title = $item->title;
		
		$_GET['s'] = $_GET['s'] ?? 1;
		$step = $_GET['s'];
		

		$this->tpl_vars['answer'] = $answ =  $this->getAnswer($item);
		$this->tpl_vars['answer_date'] = @explode(' ',$answ->insert_time)[0];
		
		if(in_array($this->steps_current, ['preview', 'sign_basic', 'sign_marksign']) && ! $answ->id){
			$this->setError(GW::ln('/m/PLEASE_COMPLETE_PREVIOUS_STEPS'));
			$this->jump2Step(1);
		}
		
		
		
		/*
		if($step > 3 && ! $answ->signature){
			$this->setError(GW::ln('/m/PLEASE_COMPLETE_PREVIOUS_STEPS'));
			$this->jump2Step(3);
		}*/
		

		GW_Composite_Data_Object::prepareLinkedObjects($item->form->elements, 'optionsgroup');
		
		$form_elms = [];
		$form_elms_grpd = [];
		
		foreach($item->form->elements as $fieldname => $e){
			
			if(!$e->active)
				continue;
			
			
			
			if($this->steps_current=='preview'){
				$vals[$fieldname] = ($answ->get("keyval/$fieldname") ?: '<'.GW::ln('/m/INPUT').': '.$e->title.'>');	
			}else{
				$vals[$fieldname] = $answ->get("keyval/$fieldname");
			}
			
			
			$options = [];
			
			if($e->type=='select'){
				$cfg = json_decode($e->config, true);
				
				
				if(isset($cfg['options_ln'])){
					$options = GW::ln($cfg['options_ln']);
					$vals[$fieldname] = $options[$vals[$fieldname]] ?? '';
				}
			}
			
			if(in_array($e->type, ['radio','checkboxes','select'])  && $e->options_src){
				$options = $e->optionsgroup->getChildOptions();	
				$e->options = $options;
			}
			
			
			if($e->type=='checkboxes'){
				$vals[$fieldname] = json_decode($vals[$fieldname], true);
				$tploptions[$fieldname] = $options;
			}elseif($options){
				
				//formoje bus pateikta kaip key, o suformuotame dokumtente kaip verte
				if($this->steps_current!='form'){
					$vals[$fieldname] = $options[$vals[$fieldname]] ?? '';
				}
			}
			
			$e->value = $vals[$fieldname];
			
			$form_elms[$fieldname] = $e;
			$form_elms_grpd[$e->fieldset][$fieldname] = $e;
		}
		
		//d::dumpas($vals);
		
		
		//sasajos su vartotojo laukais / uzkrauti anksciau uzpildytas vertes
		if($this->app->user)
		foreach($item->form->elements as $e){
			if($e->linkedfields){
				foreach($e->linkedfields as $field){
					list($obj, $key) = explode('/',$field,2);
					
					if($obj=='user'){
						$vals[$e->fieldname] = $this->app->user->get($key);
					}
				}
			}
		}
		
		$this->tpl_vars['options'] = $tploptions;
		$this->tpl_vars['form_elements'] = $form_elms;
		$this->tpl_vars['grouplist'] = $form_elms_grpd;
		
		
		$this->tpl_vars['form'] = $vals;
		$this->tpl_vars['answer'] = $answ;
		$this->tpl_vars['user'] = $this->app->user;
		$this->tpl_vars['SIGNATURE'] = "abc";
		
		//gauti paraso data jei tokios nera tada paduoti siandienos data
		$this->tpl_vars['CONTRACT_DATE'] = $answ->sign_time && strpos($answ->sign_time,'0000')!==0 ? date('Y-m-d',strtotime($answ->sign_time)) : date('Y-m-d');
		
		$this->smarty->assign($this->tpl_vars);
		
		
		
	
		
		
		
		//d::dumpas($item->body);
		
		$signature= $this->smarty->fetch('string:'.file_get_contents(__DIR__.'/tpl/signature.tpl'));
		//$this->tpl_vars['SIGNATURE'] = $signature;
		
		$body = $item->body;
		$body = str_replace('{$SIGNATURE}', $signature, $body);
		
		$body= $this->smarty->fetch('string:'.$body);
		
		$this->tpl_vars['body'] = $body;
		
		
		
		if($this->steps_current=='sign_marksign'){
			$answer = $answ;
			$info = $answer->temporary_signing;
			$sign_details = explode('|', $info);
			
			if(!$info || $sign_details[2] < time() || isset($_GET['reinit'])){
				$info = $this->__exportToMarkSign();
			}
			
			if(isset($_GET['reinit'])){
				navigator::jump(false, ['reinit'=>null]+$_GET);
			}
			
			
			
			
			$sign_details = explode('|', $info);
			$this->tpl_vars['document_id'] =$sign_details[0];
			$this->tpl_vars['url'] = $sign_details[1];
			$this->tpl_vars['valid_until'] =$sign_details[2];
			
			d::ldump($answer->temporary_signing);
			
			//stadija kai jau pasirasyta
			d::ldump($answer->keyval->signed_document_filename);
		}



		
		
		//if($_GET['s'] ?? 0 == 4){
		//	$this->__sendMail($item);
		//}
	}
	
	
	private $admin_access=false;
	
	function getAnswer($doc, $create=false)
	{
		if($this->admin_access){
			$answ = GW_Form_Answers::singleton()->find(['id=?',$_GET['answerid']]);
			return $answ;
			
		}
		
		$initial = [
		    'owner_id'=>$doc->form->id,
		    'user_id'=>$this->app->user->id,
		    'doc_id'=>$doc->id
		];
		
		if($this->modconfig->allow_sign_again)
			$initial['sequence'] = $_GET['multiple'] ?? 1;
		
		if(isset($_GET['obj'])){
			list($obj_type,$obj_id)=explode('~', $_GET['obj']);
			$initial['obj_type'] = $obj_type;
			$initial['obj_id'] = $obj_id;
		}
		
		$answer = GW_Form_Answers::singleton()->find(GW_DB::buidConditions($initial));
		
		if(!$answer){
			$answer = GW_Form_Answers::singleton()->createNewObject($initial);
			
			if($create){
				$answer->insert();
			}
		}
		
		return $answer;
	}
		
	function prepareNewAnswer($item)
	{
		$answer = $this->getAnswer($item, true);
		
		
		/*
		if($answer->signature && ($_GET['s']??false)!=4  && !isset($_GET['pdf'])){
			$this->setError(GW::ln('/m/ALREADY_SIGNED').'.');
			$this->jump2Step(4);
		}
		*/
		
		$vals['owner_id'] = $item->form->id;
		$vals['user_id'] = $this->app->user->id;
		$vals['ln'] = $this->app->ln;
				
		$answer->setValues($vals);
		$answer->save();
		
		return $answer;
	}
	
	function doSubmitForm()
	{
		$this->userRequired();
		$this->initSteps();
		
		$item = $this->getDataObjectById();
		$answer = $this->prepareNewAnswer($item);
		
		////////// WARNING FIELDS SHOULD BE CHECKED
		$vals = $_POST['item'];
		$answer->setValues($vals);
				
		//d::dumpas($vals);

		//sasajos su vartotojo laukais // issaugoti-atnaujinti
		if($this->app->user){
			foreach($item->form->elements as $e){

				if($e->linkedfields){
					foreach($e->linkedfields as $field){
						list($obj, $key) = explode('/',$field,2);

						if($obj=='user'){
							$this->app->user->set($key, $vals["keyval/".$e->fieldname]);
						}
					}
				}
			}
			$this->app->user->updateChanged();
		}
			
		$answer->save();
		
		$this->setMessage(GW::ln('/m/FORM_ACCEPTED_VERIFY_AND_SIGN'));
		
		$this->jump2Step($_GET['s']+1);
	}
	
	function jump2Step($step)
	{		
		$this->initSteps();
		
		if(!is_numeric($step)){
			$seekidx = $this->getStepIdx($step);
			//d::dumpas([$step, $this->steps, $seekidx]);
			$step = $seekidx!==false ? $seekidx : $step;;
		}
			
		
		$this->app->jump(false,['id'=>$_GET['id'],'s'=>$step]);
	}
	
	function doSign()
	{
		$this->userRequired();
		
		$item = $this->getDataObjectById();
		
		$answer =  $this->getAnswer($item);
		
		if(!$answer->id){
			$this->setError(GW::ln('/m/PLEASE_COMPLETE_PREVIOUS_STEPS'));
			$this->jump2Step(1);
		}
		
		
		
		
		if($answer->signature){
			$this->setError(GW::ln('/m/ALREADY_SIGNED').'...');
			$this->jump2Step($_GET['s']);
		}		
	
		
		$this->signature($answer, true);
		$answer->updateChanged();
		
		
	
		$this->__sendMail($item);
		
		$this->setMessage(GW::ln('/m/DOC_SIGN_SUCC'));
		
		
		$this->jump2Step($_GET['s']);
		exit;
	}
	
	function signature($item, $set=false)
	{		
		
		if($set==true){
			$signature = implode(' || ',["USERID:".$this->app->user->id,time(),$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
			$item->set('signature', $signature);
			$item->sign_time = date('Y-m-d H:i:s');
		}elseif($set===null){
			$item->set('signature', null);		
		}else{
			return $item->get('signature');
		}
	}
	
	function __sendMail($doc)
	{
		$docs = $this->doExportAsPdf(false);
				
		$opts = [
			'to' => $this->app->user->email,
			'body'=> GW::ln('/m/SIGNED_DOCUMENT_ATTACHED'),
			'subject' => GW::ln('/m/DOCUMENT_SIGNED') .' - '. $doc->title,
			'attachments' => [
			    $doc->idname.'.html' => $docs['html'], 
			    $doc->idname.'.pdf' => $docs['pdf']
			],
			'bcc'=>explode(';',$doc->admin_emails)
		];
		
		
		//d::dumpas($opts);
				
		GW_Mail_Helper::sendMail($opts);			
	}
	
	
	function doExportAsPdf($out2Screen=true)
	{
		$_GET['pdf'] = 1;
		
		if(!isset($this->tpl_vars['item']))
			$this->viewItem();
		
		$doc = $this->tpl_vars['item'];
		
		$html = $this->tpl_vars['body'];
		
		$html = " <style>*{ font-family: DejaVu Sans !important;}</style>".$html;
		
		$digital= $this->smarty->fetch('string:'.file_get_contents(__DIR__.'/tpl/digitalsignature.tpl'));
		$html.=$digital;
		
		
		$dpi = $this->tpl_vars['item']->get('config/dpi') ? : 150;
				
		$pdf = GW_html2pdf_Helper::convert($html, false, ['params'=>['dpi'=>$dpi]]);
		
		
		if(!$out2Screen)
			return ['html'=>$html, 'pdf'=>$pdf];
		
		header("Content-type:application/pdf");
		header("Content-Disposition:inline;filename=".$doc->idname.".pdf");
		die($pdf);			
	}
	
	
	
	function marksignCall($url, $payload = false, $headers)
	{
		$ch = curl_init($url);



		// Attach encoded JSON string to the POST fields
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

		// Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		// Return response instead of outputting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Execute the POST request
		$result = curl_exec($ch);

		
		
		// Get the POST request header status
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		

		/*
		// If header status is not Created or not OK, return error message
		if ( $status !== 201 || $status !== 200 ) {
		   die("Error: call to URL $url failed with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
		}*/

		// Close cURL resource
		curl_close($ch);

		
		
		// if you need to process the response from the API further
		$response = json_decode($result, true);
		
		
		//d::ldump([$result, $status, $response]);

		return $response;
		//return json_decode($data);
	}	
	
	//todo padaryt kad access token imtu is konfigo
	//todo padaryt reasonable pdf pavadinima?
	function __exportToMarkSign()
	{
		

		
		if(!isset($this->tpl_vars['item']))
			$this->viewItem();
		
		$doc = $this->tpl_vars['item'];
		
		
		$answer = $this->getAnswer($doc);
		$answer->setSecretIfNotSet(true);
		
		
		$response = $this->doExportAsPdf(false);
		
		$headers =[];
		$headers['content-type']='application/json';
		$payload = [
			'access_token'=>'565400d1-06ab-9fbd-5e14-699245405579',
			'callback_url'=>'https://contracts.tmcvolley.lt/lt/direct/docs/docs/marksignaccept?answer_id='.$answer->id.'&verify='.$answer->secret,
			"file"=>[
			    "filename"=> "test.pdf",
			    "content"=> base64_encode($response['pdf'])
			]
		];
		
		
		//d::dumpas();

		$result =$this->marksignCall("https://api.marksign.eu/api/document/generate-temporary-signing-link.json", $payload, $headers);
		
		if($result['status']=='ok'){
		
			$tmp_link = $result['temporary_signing_links'][0]['temporary_signing_link'];
			$tmp_valid = $result['temporary_signing_links'][0]['valid_until'];
			$answer->temporary_signing = implode('|', [$result['document_id'], $tmp_link, $tmp_valid]);
			

			$answer->updateChanged();
			
			//d::dumpas($result);
			
			
			return $answer->temporary_signing;
		}else{
			$this->setError("Sorry. we are experiencing difficulties. Cant connect with signing provider, please try again later");
			
			$this->jump2Step($_GET['s']-1);
		}
		
		
				
	}
	
	//todo padaryt kad access token imtu is konfigo
	//todo padaryt reasonable pdf pavadinima?
	function __downdoadSigned($answer)
	{		
		
		//d::dumpas($answer->temporary_signing);
		$sign_info = explode('|', $answer->temporary_signing);
		$document_id = $sign_info[0];
		
		
		
		$pdfcontent  = file_get_contents($api_url="https://api.marksign.eu/api/document/{$document_id}/download.json?access_token=565400d1-06ab-9fbd-5e14-699245405579");
				
		
		file_put_contents($fn = GW::s('DIR/REPOSITORY').'contracts/'.$answer->id.'.pdf', $pdfcontent);
		
		$answer->keyval->signed_document_filename = $fn;
		$answer->sign_time = date('Y-m-d H:i:s');
		
		
		return strlen($pdfcontent);
	}
	
	
	function viewMarksignAccept()
	{
		$answer= GW_Form_Answers::singleton()->find($_GET['id']);
		if($answer && $answer->secret == $_GET['verify']){
			$sz = $this->__downdoadSigned($answer);
		}
		
		file_put_contents(GW::s('DIR/LOGS').'mark_sign', 
			json_encode(['get'=>$_GET,'post'=>$_POST, 'server'=>$_SERVER, 'date'=>date('Y-m-d H:i:s'), 'downloaded_size'=>$sz], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
			FILE_APPEND
		);
	}
	
	function doWaitSigned()
	{
		
		$this->app->sessionWriteClose();
		
		$answer= GW_Form_Answers::singleton()->find($_GET['id']);
		
		for($i = 0; $i < 20; $i++){
			
			if($answer->keyval->signed_document_filename){
				die("signed");
			}
			
			$answer->extensions['keyval']->obj->cacheClear();
			
			sleep(3);
		}
		die("answer {$answer->id} timeout");
	}
	
	function doTestWaitSigned()
	{
		$answer= GW_Form_Answers::singleton()->find($_GET['id']);
		
		
		if($answer->keyval->signed_document_filename){
			$answer->keyval->signed_document_filename = "";
			d::dumpas('isvalytas');
		}else{
			$answer->keyval->signed_document_filename = "testas";
			d::dumpas('nustatytas');
		}
		
		
	}
	
	
	function viewDocument()
	{		
		$adminid = $_SESSION['cms_auth']['user_id'] ?? false;
		//d::dumpas($adm=GW_User::singleton()->find(['id=?', $adminid]));
		/*
		d::dumpas([
		  $adminid,
		    $adm=GW_User::singleton()->find(['id=?', $adminid]), 
		    GW_Permissions::canAccess('form/answers', $adm->group_ids),
		    $adminid && $adm=GW_User::singleton()->find(['id=?', $adminid]) && GW_Permissions::canAccess('form/answers', $adm->group_ids)
		]);
		*/
		
		if($adminid && ($adm=GW_User::singleton()->find(['id=?', $adminid])) && GW_Permissions::canAccess('forms/forms', $adm->group_ids)){
			//d::dumpas("hello {$adm->title}");
			$this->app->user = $adm;
		}elseif(!$this->app->user || !GW_Permissions::canAccess('forms/forms', $this->app->user->group_ids))
		{
			$this->userRequired();
			return $this->setError("Admin only");
		}
		
		$this->admin_access = true;
		$this->viewItem();
	}
}