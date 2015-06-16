<?php


class GW_Common_Module extends GW_Module
{
	var $allow_auto_actions=
	[
		'dosave'=>1,
		'dodelete'=>1,
		'doinvertactive'=>1, 
		'domove'=>1,
		'viewform'=>1, 
		'viewlist'=>1,
		'viewdialogconfig'=>1,
		'dodialogconfigsave'=>1,
		'doclone'=>1,
	];
	
	var $filters=Array();
	
	//to easy adjust list for printing
	var $paging_enabled=true;
	
	// 1 - integer
	var $data_object_id_type = 1;
	
	// share with smarty
	var $options;
	
	/**
	 * to use this function you must store in $this->model GW_Data_Object type object
	 */
	
	function init()
	{
		parent::init();
		
		$this->list_params['paging_enabled']=false;
		
		//specifu model name in lang file
		if(! isset($this->model) && ($tmp = $this->app->page->getDataObject()))
			$this->model = $tmp;
		
		$this->smarty->assignByRef('opt', $this->options);
	}
	
	
	function getCurrentItemId()
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
		
		if($id)
			return $id;
		
		if(isset($this->app->path_arr_parent['data_object_id']) && $tmp = $this->app->path_arr_parent['data_object_id'])
			$id = $tmp;	
		
		if($this->data_object_id_type == 1)
			$id = (int)$id;		
		
		return $id;
	}
	
	function getDataObjectById($load=true, $class=false)
	{
	
		$id = $this->getCurrentItemId();
		
		
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
		$this->app->setMessage($this->app->lang['ITEM_REMOVE_SUCCESS']);
		
		$this->fireEvent('AFTER_DELETE', $item);
		
		$this->jump();
	}
	
	function common_doClone(){
		
		if(! $item = $this->getDataObjectById())
			return false;
			
		$this->canBeAccessed($item, true);
		
		$this->fireEvent('BEFORE_CLONE', $item);
		$this->__doCloneAfterClone($item);
		
		
		$_REQUEST['item'] = $item->toArray();
		$_REQUEST['item']['id']=0;
		
		$_SESSION['item']=$_REQUEST['item'];
		
		$this->jump();
	}
	
	function __doCloneAfterClone($item)
	{
		$item->title = $item->title.' ('.$this->app->lang['ITEM_COPY'].')';
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
		
		if(isset($_REQUEST['SAVE-TYPE']) && $_REQUEST['SAVE-TYPE']=="INSERT")
			$item->insert();
		else
			$item->save();
			
		$this->fireEvent('AFTER_SAVE', $item);	
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->app->setMessage($this->app->lang['SAVE_SUCCESS']);		
		
		$this->jumpAfterSave($item);
	}

	function jumpAfterSave($item=false)
	{	
		//show last operated item in list
		if($item)
			$_REQUEST['id']=$item->get('id');
		
		if($_REQUEST['submit_type']==1){//apply
			$options = $item ? Array('id'=>$item->get('id')) : Array();
			$this->jump(false, $options+$_GET);
		}else{ //save
			
			if($tmp = $_REQUEST['return_to'])
				return $this->jump($tmp);
			
			$this->jump(dirname($this->app->path));
		}		
	}	
	
	/**
	 * common view - viewForm. override this if diferent functionality needed
	 */	
	function common_viewForm()
	{
		$item = $this->model->createNewObject();
		
		$id = $this->getCurrentItemId();
		
		
		
		//only form i18n objects
		if(isset($this->i18n_fields) && $this->i18n_fields)
			$item->_lang = $this->lang();		
		
		//pvz kelias: articles/77/form
		//istrauks 77

		

		
		//if we encounter error during the submit
		//fill out form with values that user submited
		if(isset($_REQUEST['item']) && $vals=$_REQUEST['item']){

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
		$this->smarty->assignByRef('item', $item);
		
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
		if(isset($this->list_params['views']['conditions']) && $this->list_params['views']['conditions'])
			$cond .= ($cond?' AND ':''). $this->list_params['views']['conditions'];
			
		$search=isset($this->list_params['filters']) ? (array)$this->list_params['filters'] : array();
		
		
		
		foreach($this->filters as $key => $val)
			$search[$key]=Array('=',$val);
								
		foreach($search as $field => $val)
		{
			$compare_type = isset($val[0]) ? $val[0] : '=';
			$value = isset($val[1]) ? $val[1] : null;
			
			if($value==='' || $value===null)
				continue;
			
			
			$cond.= ($cond ? ' AND ':'');
			
			$value=GW_DB::escape($value);
			
			if(method_exists($this, $ofmethod="overideFilter$field")) {
				$cond.=$this->$ofmethod($value);
			} else {					
				switch($compare_type)
				{
					case 'IN':
						$opt = array_splice($val,1);
						$cond .= "(`$field` IN ('".implode("','",$opt)."'))";	
						break;
					case 'LIKE':
						$cond.="(`$field` LIKE '%".$value."%')";
						break;
					case 'LIKE%,,%':
						$cond.="(`$field` LIKE '%,".$value.",%')";					
					default:
						$cond.="(`$field` = '".$value."')";
						break;
				}
			}
		}
		
		if(isset($this->list_params['order']) && $ord=$this->list_params['order'])
			$params['order']=$ord;
			
		if(isset($this->list_params['views']['order']) && $ord=$this->list_params['views']['order'])
			$params['order']=$ord;
	}
	
	function setDefaultOrder()
	{
		if(!isset($this->list_params['order']) || !$this->list_params['order'])
			$this->list_params['order']=$this->model->getDefaultOrderBy();
	}
	
	function loadViews()
	{
		$views = $this->app->page->VIEWS;
		$store =& $this->list_params['views'];
		$default = false;
		
		$alli18n = $this->app->lang['FILTER_ALL'];
		
		$views = Array($alli18n=>Array('name'=>$alli18n,'conditions'=>'','default'=>1)) + (array)$views;
		
		foreach($views as $i => $view)
			if(isset($view['default']))
				$default = $view;
			
		if(!$store['name'])
			$store=$default;
					
		foreach($views as $i => $view)
		{		
				
			if($store['name']==$view['name'])
			{
				$store=$view;
				$views[$i]['active']=1;
			}
				
			if(isset($view['calculate']))
			{
				$key=$this->app->page->path.'::views::'.$view['name'];
				
				if(!($views[$i]['count'] = GW_Session_Cache::get($key)))
				{
					$views[$i]['count'] = $tmp = $this->model->count($view['conditions']);
					GW_Session_Cache::set($key, $tmp, '10 seconds');
				}
			}
		}
				
		$this->smarty->assign('views', $views);
	}
	
	function loadOrders()
	{
		$orders = $this->app->page->ORDERS;
		$store =& $this->list_params['orders'];
		$default = false;
		
		$defi18n_name = $this->app->lang['DEFAULT'];
		
		$orders = Array('default'=>Array('name'=>$defi18n_name,'order'=>$this->model->getDefaultOrderBy(),'default'=>1)) + (array)$orders;
		
		foreach($orders as $i => $order)
			if(isset($order['default']))
				$default = $order;
			
		if(!$store['name'])
			$store=$default;
					
		foreach($orders as $i => $order)
		{		
				
			if(isset($store['name']) && $store['name']==$order['name'])
			{
				$store=$order;
				$orders[$i]['active']=1;
			}
		}
						
		$this->smarty->assign('list_orders', $orders);		
	}
	
	function doSetView()
	{
		$this->list_params['views']['name'] = $_REQUEST['name'];
		
		unset($_GET['name']);
				
		
		$this->jump();
	}
	
	function doSetOrder()
	{
		
		$orders = $this->app->page->ORDERS;
		
		$foundorder=false;
		
		
		
		
		if(isset($_REQUEST['name']))
		{
			$defi18n_name = $this->app->lang['DEFAULT'];
			
			if($_REQUEST['name']==$defi18n_name)
				$orders[] = array('name'=>$defi18n_name,'order'=>$this->model->getDefaultOrderBy());


			foreach($orders as $order)
				if($order['name']==$_REQUEST['name'])
					$foundorder = $order;

			if(!$foundorder){
				$this->setErrors('/GENERAL/ORDER_NOT_FOUND'); 
				$this->jump();
			}

			$this->list_params['order'] = $foundorder['order'];

			$this->list_params['orders']['name'] = $_REQUEST['name'];
		}elseif(isset($_REQUEST['order'])){
			
			if(!$this->__validateOrder($_REQUEST['order'], $this->model->getColumns()))
			{
				$this->setErrors('/GENERAL/BAD_ORDER_FIELD'); 
				$this->jump();
			}else{				
				$this->list_params['orders']['name'] = 'NIEKAS';
				$this->list_params['order'] = $_REQUEST['order'];
			}
		}
		
		
		
		unset($_GET['name']);
		
		$this->jump();
	}
	
	function __parseOrders($order)
	{
		preg_match_all('/(\w+) (ASC|DESC)/i', $order, $matches, PREG_SET_ORDER);
			
		return $matches;	
	}
	
	//pereina per visus stulpelius (norimus rikiuoti) ir patikrina ar yra stulpeliu sarase
	//jei nera arba jei nematchina grazina false
	function __validateOrder(&$order, $columns)
	{
		if($matches = $this->__parseOrders($order))
		{
			foreach($matches as $match)
				if(!isset($columns[$match[1]]))
					return false;
				
			
			$orders=[];
			foreach($matches as $match)
				$orders[]=$match[1].' '.$match[2];
			
			$order=implode(',', $orders);
				
			return true;
		}else{
			return false;
		}
	}
	
	
	function common_viewList($params=Array())
	{
		$this->loadViews();
		$this->loadOrders();
		
		$cond=isset($params['conditions']) ? $params['conditions'] : false;
		
		$this->setListParams($cond, $params);
		
		if($this->paging_enabled && $this->list_params['paging_enabled'] && $this->list_params['page_by'])
		{
			$page = isset($this->list_params['page']) && $this->list_params['page'] ? $this->list_params['page']-1 : 0;
			$params['offset']=$this->list_params['page_by']*$page;
			$params['limit']=$this->list_params['page_by'];
		}
		$params['key_field']=$this->model->primary_fields[0];
		
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
		

		if($saved = (array)$this->app->page->fields)
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
			
		
		$this->app->page->fields = $fields;
		$this->app->page->update(Array('fields'));
		
		$this->jump();
	}
	
	
	function common_viewDialogConfig()
	{
		$fields = $_SESSION['current_module_fields'];
		$saved = (array)$this->app->page->fields;
		

		$this->smarty->assign('fields', $saved + $fields);
		
		ob_flush();
	}
	
	function getMoveCondition($item)
	{
		return GW_SQL_Helper::condition_str($this->filters);
	}
	
	function methodExists($name)
	{
		return method_exists($this,$name) || isset($this->allow_auto_actions[$name]);
	}
	
	function canBeAccessed($item, $die=true)
	{
		$result = true;//$item->canBeAccessedByUser($this->app->user);
		
		if(!$die || $result)
			return $result;

		$this->setErrors('/GENERAL/ACTION_RESTRICTED');
		$this->jump();
	}	
	
	function __call($name, $arguments)
	{	
		$name = strtolower($name);
		
		if(isset($this->allow_auto_actions[$name]))
			return call_user_func_array(Array($this, "common_$name"), $arguments);
		else
			trigger_error('method "'.$name.'" not exists', E_USER_NOTICE);
	}
}