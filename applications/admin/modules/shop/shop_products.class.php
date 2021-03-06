<?php


class Shop_Products extends GW_Composite_Data_Object
{
	public $calculate_fields = [
	    'keyval'=>1, //product extension
	    'discount_display'=>1
	];
	public $ignore_fields = [
	    'keyval' => 1
	];	
	public $composite_map = [
		'typeObj' => ['gw_composite_linked', ['object'=>'Shop_ProdTypes','relation_field'=>'type']],
		'parent' => ['gw_composite_linked', ['object'=>'Shop_Products','relation_field'=>'parent_id']],
		'image' => ['gw_image', ['dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100']],
	];	

	
	public $ownerkey = 'shop/products';
	public $extensions = ['keyval'=>1, 'attachments'=>1];
	
	
	function calculateField($key)
	{
		switch ($key) {

			case 'keyval':
				return $this->extensions['keyval'];
			break;
			case 'discount_display':
				$dif = $this->oldprice - $this->price;
				return $dif  ? round(($dif / $this->oldprice)*100).'%' : '';
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
	function orderItemPayd($unit_price, $qty, $log_entry)
	{
		$this->qty = $this->qty - $qty;
		$this->updateChanged();
	}
	

	
	function __get($key)
	{
		if(GW::$context->app->app_name != 'SITE' || !$this->get('parent_id') || !$this->get('parent')){
			return parent::__get($key);
		}
		
		if($tmp = $this->get($key))
			return $tmp;
		
		return $this->get('parent')->get($key);
	}
	
	
}