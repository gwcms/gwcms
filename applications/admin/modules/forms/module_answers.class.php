<?php


class Module_Answers extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{
		$this->model = GW_Form_Answers::singleton();
		
		$this->app->carry_params['user_id']=1;
		$this->app->carry_params['clean']=1;
		
		
		
		$this->owner_id=$this->app->path_arr[1]['data_object_id'] ?? false;	
		$this->app->carry_params['doc_id']=1;
		$this->app->carry_params['obj_type']=1;
		$this->app->carry_params['obj_id']=1;
		
		
		
		
		
		//$this->filters['user_id']=$_GET['user_id'];
		if($this->owner_id)
			$this->filters['owner_id']=$this->owner_id;
		
		parent::init();
		
		if(isset($_GET['doc_id']))
		{
			$this->filters['doc_id'] = $_GET['doc_id'];
		}
		if(isset($_GET['owner_id']))
		{
			$this->owner_id = $this->filters['owner_id'] = $_GET['owner_id'];
		}	
		
		if(isset($_GET['obj_type']))
		{
			$this->obj_type = $this->filters['obj_type'] = $_GET['obj_type'];
		}
		
		if(isset($_GET['obj_id']))
		{
			$this->obj_id = $this->filters['obj_id'] = $_GET['obj_id'];
		}
		
		
		$this->initConfig();
		$this->initFeatures();
		//d::dumpas($this->filters);
		
		
		if($this->feat('itax')){
			$this->addRedirRule('/^doItax|^viewItax/i','itax');
		}
	}
	
	
	
	function viewForm()
	{
		$x = parent::viewForm();
		
		
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		
		$fields = [];
		foreach($elms as $e){
			$options=[];
			$params_expand = json_decode($e->config, true);
			
			$type = $e->type;
			if($type=='checkboxes')
				$type='multiselect';
			
			if($type=='radio')
				$type='select';
			

				
			
			$fields["keyval/{$e->fieldname}"]=[
			    'type'=>$type, 
			    "title"=>$e->title, 
			    "hidden_note"=>$e->hidden_note,
			    //'required'=>$e->required,
			    'params_expand'=>$params_expand,
			    'note'=>$e->note,
			    'options'=>$options,
			];
			
		
			
			if(in_array($e->type, ['radio','checkboxes','select'])  && $e->options_src){
				
				
			
					//itraukti pasyvias opcijas
				$fields["keyval/{$e->fieldname}"]['options'] = $e->optionsgroup->getChildOptions(true);
				
				
				if($e->type=='checkboxes'){
					$fields["keyval/{$e->fieldname}"]['value_format']='json1';
				}
			}			
						
			
			
				
						
			if(isset($params_expand['options_ln'])){	
				$fields["keyval/{$e->fieldname}"]['options'] = GW::ln($params_expand['options_ln']);
			}
		}
		
		$fields['admin_note'] = ['type'=>'text'];
		$fields['signature'] = ['type'=>'text'];
		$fields['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'users/usr'];
		$fields['user_signed'] = ['type'=>'file'];
	
		
		$fields['active'] = ['type'=>'bool'];
		$fields['ln'] = ['type'=>'select', 'options'=>$this->app->langs,'options_fix'=>1, 'empty_option'=>1];
		
		$this->tpl_vars['fields_config'] = [
		    'cols'=>2,
		    'fields'=>$fields
		];
		
			//d::ldump($fields);
			
			
		
		//d::dumpas($fields);
		
		return $x;
	}
	
	
	function getListConfig() {
		
		$cfg = parent::getListConfig();
	
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		

		foreach($elms as $e){	
			$cfg['fields'][$e->fieldname]="Lof";
		}	
		
		$cfg['fields']['user_actions']='Lof';
		
		if($this->feat('itax')){
			$cfg['fields']['itax_stat'] = 'Lof';
		}
		
		
		
		return $cfg;
	}
	
	function __eventAfterListParams(&$params)
	{
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		

			
		
		$params['select'] = 'a.*';
		
		$jnum =0;	
			
		foreach($elms as $e)
			if($this->list_config['display_fields'][$e->fieldname] ?? false){
				$jnum++;
				$fieldname = GW_DB::escape($e->fieldname);
				$fieldname2 = GW_DB::escapeField($e->fieldname);
				
				$params['joins'][] = [
				    'left',
				    'gw_form_answers_extended AS pext'.$jnum,
				    'pext'.$jnum.'.owner_id=a.id AND pext'.$jnum.'.key = "'.$fieldname.'"'];
				
				$params['select'].=', pext'.$jnum.'.value AS '.$fieldname2;
			}
			
			
			
		
	}

	function __eventAfterList(&$list){
		
		GW_Composite_Data_Object::prepareLinkedObjects($list, 'user');
	}
	

	
	/*
	function doSave()
	{
		$vals = $_POST['item'];
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		$founderror = 0;
		
		//d::dumpas("ownerid $this->owner_id");
		
		foreach($elms as $e){
			
			if(!$vals[$e->fieldname] && $e->required){
				$error = [];
				$error['type'] = GW_MSG_ERR;
				$error['field'] = $e->fieldname;
				$error['text'] = GW::l('/G/validation/REQUIRED');
				
						
				$this->setMessage($error);
				$founderror=1;
			}
			
		}

		
		if($founderror){
			$this->processView('form');
			exit;			
		}else{
			
			$valsold = GW_Form_Vals::singleton()->findAll(['owner_id=?', $this->owner_id],['key_field'=>'fieldname']);
			
			foreach($elms as $e){
				
				if(isset($valsold[$e->fieldname])){
					$valentry = $valsold[$e->fieldname];
				}else{
					$valentry = GW_Form_Vals::singleton()->createNewObject();
					$valentry->fieldname = $e->fieldname;
					$valentry->field_id = $e->id;
					$valentry->owner_id = $this->owner_id;					
				}
				
				
				$valentry->value = $vals[$e->fieldname];
				//d::ldump($vals[$e->fieldname]);
				
				$valentry->save();
			}
			
			$this->setMessageEx(['text'=>'/g/SAVE_SUCCESS', 'float'=>1, 'type'=>GW_MSG_SUCC]);
		}
	}
	 * 
	 */
		
	/*
	$fields_config=[
	cols=>2,
	fields=>[
		modif_title=>[type=>text, colspan=>3],
		image=>[type=>image, colspan=>1],
		"keyval/description"=>[type=>textarea,colspan=>1],
		
		title=>[type=>text, colspan=>1],
		type=>[modpath=>'products/prodtypes', colspan=>1]+$sel_ajax,
		active=>[type=>bool, colspan=>1],
		price=>[type=>number, colspan=>1],
		price_scheme=>[type=>text,colspan=>1]
		
		
	]
]	
	 * 
	 */

	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($item){ return $item->admin_note;  },
		    'search_fields'=>['admin_note']
		];	
		    
		if($this->owner_id){
			$opts['condition_add'] = "owner_id=".(int)$this->owner_id;
		}

		return $opts;	
	}

	function doSyncWithLinked()
	{
		$item = $this->getDataObjectById();
		
		$list = [];
			
		//sasajos su vartotojo laukais // issaugoti-atnaujinti
		if($item->user_id && $item->user){
			$user = $item->user;
			foreach($item->form->elements as $e){

				if($e->linkedfields){
					foreach($e->linkedfields as $field){
						list($obj, $key) = explode('/',$field,2);

						if($obj=='user'){
							$prev = $user->get($key);
							$user->set($key, $val=$item->get("keyval/".$e->fieldname));
							$list[]=['user_id'=>$user->id,'formfield'=>$e->fieldname,'field'=>$key, 'prev_value'=>$prev, 'value'=>$val];
						}
					}
				}
			}
			
			//d::ldump($user);
			$user->updateChanged();
			//d::ldump(GW_Data_to_Html_Table_Helper::doTable($list));
			//d::dumpas($user);
		}
		
		if($list){
			$this->setMessage("Updated data: <br/>".GW_Data_to_Html_Table_Helper::doTable($list));
		}
		
		$this->jump();
	}
	
	
	function doCopyAnswerToOtherDoc()
	{
		$item = $this->getDataObjectById();
		
		
		$form = ['fields'=>[
			'anotherdoc' => ['type'=>'select_ajax','modpath'=>"docs", 'required'=>true, "width"=>"300px",'options'=>'', 'preload'=>1], //is vartotojo $item->set('ext/itax_suplier_id')
			'signature_copy' => ['type'=>'bool']
		],'cols'=>1];
		
		
		
		
		if(!($answers=$this->prompt($form, "Pasirinkite dokumentą į kurį kopijuoti")))
			return false;	
				
		
		$keyval_vals = $item->extensions['keyval']->getAll();

		
		$new = GW_Form_Answers::singleton()->createNewObject();
		
		$new->owner_id = $item->owner_id;
		$new->doc_id = $answers['anotherdoc'];
		$new->owner_id = $item->owner_id;
		$new->ln = $item->ln;
		
		
		$original_url = "/admin/".$this->app->ln."/forms/forms/".$item->owner_id."/answers/".$item->id."/form?doc_id=".$item->doc_id;
		$new->admin_note = "Copy from $original_url";
		
		if($answers['signature_copy'] ?? false){
			$new->signature = $item->signature;
			
		}
		
		$new->insert();
		
		foreach($keyval_vals as $key => $val)
		{
			$new->set("keyval/$key", $val);
		}
		
		
		$copy_url = "/admin/".$this->app->ln."/forms/forms/".$new->owner_id."/answers/".$new->id."/form?doc_id=".$new->doc_id;
		
		$this->setMessage("Copy create success. <a class='btn btn-primary' href='".$copy_url."'>Navigate to new answer</a>");
		//po perkelimo ismesti mygtuka su nuoroda i to kito dokumento atsakymus
		$this->app->jump();
	}
	
	function doActOfAcceptance()
	{
		$form = ['fields'=>[
			'acceptance_date' => ['type'=>'date','required'=>1], //is vartotojo $item->set('ext/itax_suplier_id')
			'signature_copy' => ['type'=>'bool','default'=>1]
		],'cols'=>1];
		
		if(!($answers=$this->prompt($form, "Nurodykite priėmimo perdavimo akto datą")))
			return false;			
		
		$item = $this->getDataObjectById();
		
		$item->set('keyval/act_of_acceptance_date', $answers['acceptance_date']);
		$item->set('keyval/act_of_acceptance_signature_copy', $answers['signature_copy']);
		
		
		$actofacceptance_url = "{$item->ln}/direct/docs/docs/document?id={$item->doc->key}&answerid={$item->id}&act_of_acceptance=1";
		
		$this->setMessage("
			<a class='btn btn-primary' href='".$actofacceptance_url."&s=preview'>View html</a>
			<a class='btn btn-default' href='".$actofacceptance_url."&act=doExportAsPdf'>View pdf</a>
			");
		//$item->
		//
		
		///{$item->ln}/direct/docs/docs/document?id={$item->doc->key}&answerid={$item->id}&s=preview"
	}
}