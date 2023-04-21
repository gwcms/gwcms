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
	
	
	function viewItem()
	{
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
		
		$this->tpl_vars['item'] = $item;
		$this->app->page->title = $item->title;
		
		$_GET['s'] = $_GET['s'] ?? 1;
		$step = $_GET['s'];
		

		$this->tpl_vars['answer'] = $answ =  $this->getAnswer($item);
		$this->tpl_vars['answer_date'] = @explode(' ',$answ->insert_time)[0];
		
		if($step > 2 && ! $answ->id){
			$this->setError(GW::ln('/m/PLEASE_COMPLETE_PREVIOUS_STEPS'));
			$this->jump2Step(1);
		}
		
		if($step > 3 && ! $answ->signature){
			$this->setError(GW::ln('/m/PLEASE_COMPLETE_PREVIOUS_STEPS'));
			$this->jump2Step(3);
		}
		
		foreach($item->form->elements as $fieldname => $e){
			
			if($step == 1){
				$vals[$fieldname] = ($answ->get("keyval/$fieldname") ?: '<'.GW::ln('/m/INPUT').': '.$e->title.'>');	
			}else{
				$vals[$fieldname] = $answ->get("keyval/$fieldname");
			}
			
			if($e->type=='select'){
				$cfg = json_decode($e->config, true);
				
				
				if(isset($cfg['options_ln'])){
					
					$options = GW::ln($cfg['options_ln']);
					$vals[$fieldname] = $options[$vals[$fieldname]] ?? '';
				}
			}
		}
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
		
		$this->tpl_vars['form'] = $vals;
		$this->tpl_vars['answer'] = $answ;
		$this->tpl_vars['user'] = $this->app->user;
		$this->tpl_vars['SIGNATURE'] = "abc";
		
		$this->smarty->assign($this->tpl_vars);
		
		
		//d::dumpas($item->body);
		
		$signature= $this->smarty->fetch('string:'.file_get_contents(__DIR__.'/tpl/signature.tpl'));
		//$this->tpl_vars['SIGNATURE'] = $signature;
		
		$body = $item->body;
		$body = str_replace('{$SIGNATURE}', $signature, $body);
		
		$body= $this->smarty->fetch('string:'.$body);
		
		$this->tpl_vars['body'] = $body;
		
		
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
		
		
		if($answer->signature && ($_GET['s']??false)!=4  && !isset($_GET['pdf'])){
			$this->setError(GW::ln('/m/ALREADY_SIGNED').'.');
			$this->jump2Step(4);
		}
		
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
		
		$item = $this->getDataObjectById();
		$answer = $this->prepareNewAnswer($item);
		
		////////// WARNING FIELDS SHOULD BE CHECKED
		$vals = $_POST['item'];
		$answer->setValues($vals);

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
		
		$this->jump2Step(3);
	}
	
	function jump2Step($step)
	{
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
			$this->jump2Step(4);
		}		
	
		
		$this->signature($answer, true);
		$answer->updateChanged();
		
		
	
		$this->__sendMail($item);
		
		$this->setMessage(GW::ln('/m/DOC_SIGN_SUCC'));
		
		
		$this->app->jump(false,['id'=>$_GET['id'],'s'=>4]);
		exit;
	}
	
	function signature($item, $set=false)
	{		
		if($set==true){
			$signature = implode(' || ',["USERID:".$this->app->user->id,time(),$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
			$item->set('signature', $signature);
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