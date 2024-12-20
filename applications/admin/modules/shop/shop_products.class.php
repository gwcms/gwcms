<?php


class Shop_Products extends GW_Composite_Data_Object
{
	public $calculate_fields = [
	    'discount_display'=>1,
	    'invoice_line'=>1,
	];	
	public $composite_map = [
		'typeObj' => ['gw_composite_linked', ['object'=>'Shop_ProdTypes','relation_field'=>'type']],
		'parent' => ['gw_composite_linked', ['object'=>'Shop_Products','relation_field'=>'parent_id']],
		'image' => ['gw_image', ['dimensions_resize'=>'1600x1600', 'dimensions_min'=> '100x100']],
	];	

	
	public $ownerkey = 'shop/products';
	
	public $extensions = [
	    'keyval'=>1, 
	    'attachments'=>1,
	    'changetrack'=>1
	];
	
	public $modif_mode_display=false;
	
		
	
	
	function calculateField($key)
	{
		switch ($key) {

			case 'discount_display':
				if((float)$this->oldprice){
					$dif = $this->oldprice - $this->price;
					return $dif  ? round(($dif / $this->oldprice)*100).'%' : '';
				}
			break;
			case 'invoice_line':
				return $this->invoice_line_over ? $this->invoice_line_over : $this->title;
			break;	
		}
	}
	
	
	function getModCounts($ids)
	{
		$q= " SELECT parent_id, count(*) AS cnt FROM `$this->table` WHERE ".GW_DB::inCondition('parent_id', $ids).' GROUP BY parent_id';
		return GW::db()->fetch_assoc($q);
		
	}
	
	
	function getPriceScheme()
	{
		$list0 = explode(';', $this->price_scheme);
		$list = [];
		
		foreach($list0 as $qty_price){
			@list($qty, $price) = explode(':', trim($qty_price));
			if($qty && $price)
				$list[$qty] = $price;
		}
		ksort($list);
		return $list;
	}
	
	
	function calcPriceScheme($units){
		$scheme = $this->getPriceScheme();
		
		foreach($scheme as $qty => $price)
			if($units >= $qty)
				$unit_price = $price;
			
		return $unit_price;
	}
	
	function calcPrice($units)
	{
		if($this->price_scheme){
			return $this->calcPriceScheme($units);
		}
		
		return $this->price;
	}
	function orderItemPayd($unit_price, $qty, $order, $orderitem)
	{
		$this->fireEvent('BEFORE_CHANGES');
		$qty_before = $this->qty;
		$this->qty = $this->qty - $qty;
		$this->updateChanged();
		
		$resp = ['qty_prev'=>$qty_before, 'qty_after'=>$this->qty];
		
		//$this->modval("after_buy_email_tpl")
		if($this->modval("after_buy_email_tpl") ){
			$url=Navigator::backgroundRequest('admin/'.$order->use_lang.'/shop/products?act=doAfterBuyEmail&id='.$orderitem->id);
			$resp['after_buy_email_act'] = $url;
		}
		if($this->modval("executor_after_buy_email_tpl") ){
			$url=Navigator::backgroundRequest('admin/'.$order->use_lang.'/shop/products?act=doAfterBuyExecutorEmail&id='.$orderitem->id);
			$resp['after_buy_executor_email_act'] = $url;
		}
		if($this->modval("notify_admin") ){
			$url=Navigator::backgroundRequest('admin/'.$order->use_lang.'/shop/products?act=doAfterBuyAdminEmail&id='.$orderitem->id);
			$resp['after_buy_admin_email_act'] = $url;
		}
		
		return $resp;
	}
	

	//ideja kad jei modifikacija tada paziurima ar uzpildytas laukas
	//jei neuzpildytas imamas aprasymas is tevinio produkto	
	function __get($key)
	{
		$val = $this->gettt($key);
		return $val;
	}
	
	function gettt($key)
	{

		
		//jei nenurodyta modifikacijos title
		if($key=='title' && $this->parent_id){
			return $this->get('parent')->title." - ".$this->modif_title;
		}	
				
		if(	
			( 
				(
				GW::$context->app->app_name != 'SITE' && 
				!GW::globals('product_modification_display_mode')
				) || 
					!$this->get('parent_id') || 
					!$this->get('parent')
			) 
				&& $key!='qty'
			){
		
			return parent::__get($key);
		}
		
		if($key=='image' || $this->isCompositeField($key)){			
			if(parent::__get($key))
				return parent::__get($key);
				
		}
		
		if(strpos($key, 'keyval/')===0){
			if(parent::__get($key))
				return parent::__get($key);
		}		
		
		
		$tmp =& $this->content_base[$key];
		

		
	
		
		if($tmp || $key=='qty' || $key=='id'){
			
			return $tmp;
		}
		
		//buvo $this->get('parent')->get($key);	
		//kai triju lygiu ivedziau tada kad is trecio lygio dasigautu iki pirmo per du persokimus
		
		return $this->get('parent')->gettt($key);		
	}
	
	
	function modval($key)
	{	
		//d::ldump([$this->id, 'key'=>$key, 'val'=>$this->$key, 'parent_id'=>$this->parent_id, ]);
		//per 3 lygius praejo testatavu
		return $this->$key || !$this->parent_id ? $this->$key : $this->parent->modval($key);
	}
	
	
	function calcPriceRange()
	{
		$modifications = $this->findAll(['active=1 AND parent_id=?',$this->id],['key_field'=>'id','order'=>'priority DESC']);

		$minprice = 99999;
		$maxprice = 0;
		foreach($modifications as $mod){
			$minprice = min($mod->price, $minprice);
			$maxprice = max($mod->price, $maxprice);
		}
		
		$this->min_price = $minprice;
		$this->max_price = $maxprice;
		$this->mod_count = count($modifications);
		$this->updateChanged();
		
	}
	
	
	function getFields()
	{
		static $cache;
		
		if($cache)
			return $cache;
		
		
		$cache =  GW_Adm_Page_Fields::singleton()->findAll(['parent=?', $this->table],['key_field'=>'fieldname']);
		
		return $cache;
	}
	
	function eventHandler($event, &$context_data = array()) {
		
		switch ($event){
			case 'BEFORE_LIST':
				//prepare fields to avoid multiple queries, last query for paging to be not interupted
				$this->getFields();
			break;
			case 'AFTER_SAVE':
				if($this->parent_id){
					$this->parent->calcPriceRange();
				}
				
			break;
			
			case 'AFTER_LOAD':
				$dynamicFields = $this->getFields();
				//d::dumpas($dynamicFields);
				
				foreach($dynamicFields as $field)
				{
					
					if($field->inp_type=="file"){
						$this->composite_map[$field->fieldname] =  ['gw_file',[]];
						
						if(isset($field->config->allowed_extensions))
							$this->composite_map[$field->fieldname][1]['allowed_extensions'] = $field->config->allowed_extensions;
					}
				}

				
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}
	
	
	public $expirity_check_before_buy = true;
	// for orders subsystem
	function expirityCheck($orderitem)
	{
		//d::dumpas(['have_amount'=>$this->qty, 'want_buy_amount'=>$orderitem->qty]);
		
		return $this->qty - $orderitem->qty >= 0;
	}		
}