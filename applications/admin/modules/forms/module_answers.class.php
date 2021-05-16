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
		
		
		
		
		//$this->filters['user_id']=$_GET['user_id'];
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
	}
	
	
	
	function viewForm()
	{
		$x = parent::viewForm();
		
		
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		
		$fields = [];
		foreach($elms as $e){
			//
			$fields["keyval/{$e->fieldname}"]=[
			    'type'=>$e->type, 
			    "title"=>$e->title, 
			    "hidden_note"=>$e->hidden_note,
			    //'required'=>$e->required,
			    'note'=>$e->note
			];
		}
		
		$fields['admin_note'] = ['type'=>'text'];
		$fields['signature'] = ['type'=>'text'];
		$fields['user_id'] = ['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'users/usr'];
		
		$fields['active'] = ['type'=>'bool'];
		$fields['ln'] = ['type'=>'select', 'options'=>$this->app->langs,'options_fix'=>1, 'empty_option'=>1];
		
		$this->tpl_vars['fields_config'] = [
		    'cols'=>2,
		    'fields'=>$fields
		];
		
		return $x;
	}
	
	
	function getListConfig() {
		
		$cfg = parent::getListConfig();
	
		$elms = GW_Form_Elements::singleton()->findAll(['owner_id=?', $this->owner_id]);
		

		foreach($elms as $e){	
			$cfg['fields'][$e->fieldname]="Lof";
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
	
}