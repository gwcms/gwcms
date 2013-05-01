<?php


class GW_Common_Module extends GW_Module
{
	var $allow_auto_actions=Array
	(
		'dosave'=>1,
		'dodelete'=>1,
		'doinvertactive'=>1, 
		'domove'=>1,
		'viewform'=>1, 
		'viewlist'=>1,
		'viewdialogconfig'=>1,
		'dodialogconfigsave'=>1,
		'doinvertbanned'=>1
	);
	
	var $filters=Array();
	
	//to easy adjust list for printing
	var $paging_enabled=true;
	
	// 1 - integer
	var $data_object_id_type = 1;	
	
	/**
	 * to use this function you must store in $this->model GW_Data_Object type object
	 */
	
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=false;
		
		//specifu model name in lang file
		if(! $this->model && ($tmp = GW::$request->page->getDataObject()))
			$this->model = $tmp;
	}
	
	function getDataObjectById($load=true, $class=false)
	{
		$id = $_REQUEST['id'];
		
		if($this->data_object_id_type == 1)
			$id = (int)$id;
		
		if(!$id)
			return $this->setErrors('/GENERAL/BAD_ARGUMENTS');
		
		if($class)
			$item = new $class($id);
		else
			$item = $this->model->createNewObject($id);
			
		if($load && !$item->load())
			return $this->setErrors('/GENERAL/ITEM_NOT_EXISTS');
			
		return $item;
	}
	
	/**
	 * common doDelete action override this if diferent functionality needed
	 */	
	function common_doDelete()
	{
		if(! $item = $this->getDataObjectById())
			return false;
			
		$this->canBeAccessed($item, true);	
		
		$this->fireEvent('BEFORE_DELETE', $item);
			
		$item->delete();
		GW::$request->setMessage(GW::$lang['ITEM_REMOVE_SUCCESS']);
		
		$this->fireEvent('AFTER_DELETE', $item);
		
		$this->jump();
	}

	
	
	public $auto_images = 1;
	
	/**
	 * common doSave action override this if diferent functionality needed
	 */
	function common_doSave()
	{
		$vals = $_REQUEST['item'];
		$vals+=$this->filters;
		$item = $this->model->createNewObject($vals, false, $this->lang());
		
		$this->fireEvent('BEFORE_SAVE_0', $item);		
		
		
		$this->canBeAccessed($item, true);
		
		
		if($this->auto_images && count($_FILES))
			GW_Image_Helper::__setFiles($item);
		
		if(!$item->validate())
		{
			$this->setErrors($item->errors);
			
			$this->processView('form');
			exit;
		}
		
		$this->fireEvent('BEFORE_SAVE', $item);
		
		//jeigu nustatomas id naujo iraso sukurimo atveju GW_Data_Object::save() funkcija interpretuoja kad norima atlikti update veiksma
		//jei i forma dadesime
		//{if !$smarty.request.id}
		//	<input type="hidden" name="SAVE-TYPE" value="INSERT">
		//{/if}
		//isvengsime sio nesklandumo
		
		if($_REQUEST['SAVE-TYPE']=="INSERT")
			$item->insert();
		else
			$item->save();
			
		$this->fireEvent('AFTER_SAVE', $item);	
		
		//jeigu saugome tai reiskia kad validacija praejo
		GW::$request->setMessage(GW::$lang['SAVE_SUCCESS']);		
		
		$this->jumpAfterSave($item);
	}

	function jumpAfterSave($item=false)
	{	
		
		if($_REQUEST['submit_type']==1){//apply
			$options = $item ? Array('id'=>$item->get('id')) : Array();
			$this->jump(false, $options+$_GET);
		}else{ //save
			
			if($tmp = $_REQUEST['return_to'])
				return $this->jump($tmp);
			
			$this->jump(dirname(GW::$request->path));
		}		
	}	
	
	/**
	 * common view - viewForm. override this if diferent functionality needed
	 */	
	function common_viewForm()
	{
		$item = $this->model->createNewObject();
		
		$id = $_REQUEST['id'];
		
		//only form i18n objects
		if($this->i18n_fields)
			$item->_lang = $this->lang();		
		
		//pvz kelias: articles/77/form
		//istrauks 77
		if($tmp = GW::$request->path_arr_parent['data_object_id'])
			$id = $tmp;
		
		//if we encounter error during the submit
		//fill out form with values that user submited
		if($vals=$_REQUEST['item']){

			$item->set('id', $vals['id']);
			$item->load();
			
			$this->canBeAccessed($item, true);
			
			$item->setValues($vals);
		}
		elseif($id){ // edit existing
			
			$item = $this->model->createNewObject($id, true, $this->lang());
			
			$this->canBeAccessed($item, true);
		}else{ // create new
			
		}
		
		$this->fireEvent("AFTER_FORM", $item);
		
		
		$this->smarty->assign('update', (int)$item->get('id'));
		$this->smarty->assign('item', $item);
		
		return $item;
	}
	
	/** 
	 * common action do:invert_active 
	 * to forbid executing 
	 * overload method 
	 */ 
	function common_doInvertActive() 
	{
		if(! $item = $this->getDataObjectById())
			return false;
        
        $this->canBeAccessed($item, true);
                 
        if(!$item->invertActive()) 
                return $this->setErrors('/GENERAL/ACTION_FAIL'); 
	 	 
        $this->jump(); 
	}	

	
	function setListParams(&$cond='', &$params=Array())
	{		
		if($this->list_params['views']['conditions'])
			$cond .= ($cond?' AND ':''). $this->list_params['views']['conditions'];
				
		$tmp1=(array)$this->list_params['filters'];
		
		foreach($this->filters as $key => $val)
			$tmp1[$key]=Array('=',$val);
						
		foreach($tmp1 as $key => $val)
		{
			list($type, $value)=$val;
			
			if($value==='' || $value===null)
				continue;
			
			$value=GW_DB::escape($value);
			
			$cond.= ($cond?' AND ':'');
			$cond.= '`'.GW_DB::escape($key)."` ".GW_DB::escape($type?$type:'=');
			
			if($type=='IN' && $val){
				$opt = array_splice($val,1);
				$cond .= '("'.implode('","',$opt).'")';								
			}elseif($type=='LIKE'){
				$cond .= "'%$value%'";
			}else{
				$cond .= "'$value'";
			}
		}
		
				
		if($ord=$this->list_params['order'])
			$params['order']=$ord;
			
		if($ord=$this->list_params['views']['order'])
			$params['order']=$ord;
	}
	
	function setDefaultOrder()
	{
		if(!$this->list_params['order'])
			$this->list_params['order']=$this->model->getDefaultOrderBy();
	}
	
	function loadViews()
	{
		$views = GW::$request->page->VIEWS;
		$store =& $this->list_params['views'];
		$default = false;
		
		$views = Array('All'=>Array('name'=>'All','conditions'=>'','default'=>1)) + (array)$views;
		
		foreach($views as $i => $view)
		{		

			if($view['default'])
				$default=$view;
				
			if($store['name']==$view['name'])
			{
				$store=$view;
				$views[$i]['active']=1;
			}
				
			if(isset($view['calculate']))
			{
				$key=GW::$request->page->path.'::views::'.$view['name'];
				
				if(!($views[$i]['count'] = GW_Session_Cache::get($key)))
				{
					$views[$i]['count'] = $tmp = $this->model->count($view['conditions']);
					GW_Session_Cache::set($key, $tmp, '10 seconds');
				}
			}
		}
		
		if(!$store['name'])
			$store=$default;
				
		$this->smarty->assign('views', $views);
	}
	
	function doSetView()
	{
		$this->list_params['views']['name'] = $_REQUEST['name'];
		
		unset($_GET['name']);
				
		
		$this->jump();
	}
	
	function common_viewList($params=Array())
	{
		$this->loadViews();
		
		$cond=$params['conditions'];
		
		$this->setListParams($cond, $params);
		
		if($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by'])
		{
			$page = $this->list_params['page']?$this->list_params['page']-1: 0;
			$params['offset']=$this->list_params['page_by']*$page;
			$params['limit']=$this->list_params['page_by'];
		}
		
		$list = $this->model->findAll($cond, $params);
		
		if($this->model->errors)
		{
			$this->list_params=Array();
			return $this->setErrors($this->model->errors);
		}
		
		$this->setDefaultOrder();//for template
		
		$this->smarty->assignByRef('list', $list);
		
		if($this->list_params['page_by'])
			$this->smarty->assign('query_info', $this->model->lastRequestInfo());
			
			
		return $list;
	}
	
	function common_doMove($params=false)
	{
		if(! ($item = $this->getDataObjectById()))
			return $this->jump();
		
		$item->move($_REQUEST['where'], $this->getMoveCondition($item));
		unset($_GET['where']);
		
		$this->jump(false, Array('id'=>$item->get('id')));
	}
	
	//used to allow user edit field list	
	function getDisplayFields($fields)
	{
		$_SESSION['current_module_fields']=$fields;
		

		if($saved = (array)GW::$request->page->fields)
			$fields = $saved+$fields; //prideti fields tam kad programavimo eigoje pridejus nauja laukeli veiktu
		
		$rez = Array();
		
		foreach($fields as $id => $enabled)
			if($enabled)
				$rez[]=$id;
		
		return $rez;
	}
		
	function common_doDialogConfigSave()
	{
		
		//atstatyti numatytuosius nustatymus
		if($_REQUEST['defaults'])
			$fields = $_SESSION['current_module_fields'];
		else
			$fields = $_REQUEST['fields'];	
			
		
		GW::$request->page->fields = $fields;
		GW::$request->page->update(Array('fields'));
		
		$this->jump();
	}
	
	
	function common_viewDialogConfig()
	{
		$fields = $_SESSION['current_module_fields'];
		$saved = (array)GW::$request->page->fields;
		

		$this->smarty->assign('fields', $saved + $fields);
		
		ob_flush();
	}
	
	function getMoveCondition($item)
	{
		return GW_SQL_Helper::condition_str($this->filters);
	}
	
	function methodExists($name)
	{
		return method_exists($this,$name) || $this->allow_auto_actions[$name];
	}
	
	function canBeAccessed($item, $die=true)
	{
		$result = true;//$item->canBeAccessedByUser(GW::$user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		$this->jump();
	}	
	
	function __call($name, $arguments)
	{	
		$name = strtolower($name);
		
		if($this->allow_auto_actions[$name])
			return call_user_func_array(Array($this, "common_$name"), $arguments);
		else
			trigger_error('method "'.$name.'" not exists', E_USER_NOTICE);
	}
}