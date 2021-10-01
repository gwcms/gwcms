<?php


class Module_Products extends GW_Common_Module
{
	use Module_Import_Export_Trait;

	/**
	 * @var GW_Product
	 */

	function init()
	{
		$this->initLogger();

		$this->config = new GW_Config($this->module_path[0].'/');
		$this->features = array_fill_keys((array)json_decode($this->config->features), 1);
		
		parent::init();
		$this->model = Shop_Products::singleton();
		$this->mod_fields = GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->model->table]);
		
		
	
		$this->list_params['paging_enabled']=1;
		
		$this->addRedirRule('/^doImport|^viewImport/i','import');
		
		
		//is import in progress
		
		
		if(isset($_GET['parent_id']))
			$this->filters['parent_id']=$_GET['parent_id'];
		
		
		if($this->filters['parent_id'] ?? false){
			$this->list_params['paging_enabled']=false;
		}
		
		
		$this->app->carry_params['clean'] = 1;
		$this->app->carry_params['parent_id'] = 1;
		$this->app->carry_params['mods'] = 1;

	}

	function __eventAfterList($list)
	{
		GW_Composite_Data_Object::prepareLinkedObjects($list, 'typeObj');
		
		foreach($this->mod_fields as $field){
			if($field->inp_type=="select_ajax"){
				$sources[$field->modpath][] = $field->fieldname;
			}
		}
		foreach($sources as $modpath => $fields){
			$model = $field->modelFromModpath();
			
			foreach($fields as $field)
				$dynfieldsopts[$field] = $model;
		}
		
		//d::Dumpas($dynfieldsopts);
		
		
		
		$ids = array_keys($list);
		
		if(isset($this->features['modifications'])){
			$cnts = Shop_Products::singleton()->getModCounts($ids);
			foreach($cnts as $pid => $cnt)
				$list[$pid]->mod_count = $cnt;
		}
		
		
		$dynopts = [];
		
		foreach($list as $item){
			foreach($dynfieldsopts as $field => $class)
				if($item->$field)
					$dynopts[$class][$item->$field]=1;
		}
		
		foreach($dynopts as $class => $ids){
			$ids = array_keys($ids);
			$this->options[$class] = $class::singleton()->findAll(GW_DB::inCondition ('id', $ids),['key_field'=>'id']);
		}
				
		$this->tpl_vars['dynfieldopts']=$dynfieldsopts;
		
		

		
		
		if($this->list_config['display_fields']['orders']==1){
			$t = GW_Order_Item::singleton()->table;
			$t1 = $this->model->table;
			$sql = "SELECT obj_id, count(*) FROM `$t` WHERE obj_type='$t1' GROUP BY obj_id";
			$rez = GW::db()->fetch_assoc($sql);
			$this->tpl_vars['count_orders'] = $rez;
		}
	}	

	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		$cfg['fields']["image"] = "L";
		
		if(isset($this->features['modifications']))
			$cfg['fields']["mod"] = "L";
		
		
		if(isset($_GET['mods'])){
			//unset($cfg['fields']["mod"]);
			$cfg['fields']["modif_title"] = "L";
		}
		
		$cfg['fields']["orders"] = "L";
		
		return $cfg;
	}		
	
	
	function doCreateModification()
	{
		$item = $this->getDataObjectById();
		$mod = $this->model->createNewObject();
		$mod->parent_id = $item->id;
		$mod->title = "Modification of ".$item->title;
		$mod->insert();
		//d::dumpas($mod);
		$this->setMessage("Mofication was created");
		
		
		Navigator::jump($this->buildUri("$mod->id/form"));
	}
	
	function __eventBeforeListParams(&$params)
	{
		//modifikacijoms
		if(!isset($_GET['parent_id']))
			$params['conditions']="parent_id=0";
	}	
	
	
	
	
	
	function createOrder($answers)
	{
		$price = $answers['price'];
		$item = Shop_Products::singleton()->find(['price=?', $price]);		
		
		$order = GW_Order_Group::singleton()->createNewObject();
		$order->extra = ['cardnr'=>$answers['cardnr'],'receiptnr'=>$answers['receiptnr']];
		$order->delivery_opt = 3;
		//$order->pay_type = 5;
		$order->payment_status = 7;;
		$order->status = 7;
		$order->insert();
		
		if($answers['insert_time'])
			$order->saveValues(['insert_time'=>$answers['insert_time']]);
		
		
		$url = $this->app->buildUri('direct/shop/products/p',['id'=>$item->id],['app'=>'site']);
		
		$cartitem = new GW_Order_Item;
		$cartitem->setValues([
			'obj_type'=>'shop_products',
			'obj_id'=>$item->id,
			'qty' => 1,
			'unit_price'=>$price,
			//'context_obj_id'=>$user->id,
			//'context_obj_type'=>'gw_customer'
			'qty_range'=>"1;1",
			'deliverable'=>10, //real item
			'link' =>$url
		]);
		
		$order->addItem($cartitem);			
	}
	
	function doCreateOrderByPrice()
	{
		$form = ['fields'=>[
		    'price'=>['type'=>'text', 'required'=>1],
		    "cardnr"=>['type'=>'text', 'required'=>1],
		    "receiptnr"=>['type'=>'text', 'required'=>1]
		],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Add order with 1 item, product will be identified by price")))
			return false;
		
		
		
		$this->createOrder($answers);	

		d::dumpas([$answers,$item, $order, $order->errors,GW::db()->last_query]);
	}
	

	function doOrdersImportSwedXml()
	{
		$form = ['fields'=>[
		    'file'=>['type'=>'file', 'required'=>1],
		],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Attach xml file")))
			return false;		
		
		$file = $answers['file']['tmp_name'];
		
		$tree = GW_XML::xmlToArray(file_get_contents($file));
		$transactions = GW_XML::simpleXmlArrFixList($tree['company']['outlet']['terminal']['batch']['card_group']['transaction']);
		//$flat = GW_Array_Helper::arrayFlattenSep('/', $tree);

		foreach($transactions as $tr){
			$answers = [
			    'price'=>$tr['paym_amount'],
			    'cardnr'=>$tr['hidden_pan'],
			    'receiptnr'=>$tr['stan'],
			    'insert_time'=>$tr['local_date'].' '.$tr['local_time'],
			];
			
			$this->createOrder($answers);
			
			$this->setMessage("Price: {$answers['price']}; Cardnr: {$answers['cardnr']}; ReceiptNr: {$answers['receiptnr']}, Time: {$answers['insert_time']}");
		}

		
	}
}

