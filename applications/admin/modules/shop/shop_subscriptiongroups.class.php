<?php


class Shop_SubscriptionGroups extends GW_Composite_Data_Object
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
		'image' => ['gw_image', ['dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100']],
	];	

	
	public $keyval_use_generic_table = 1;
	public $ownerkey = 'shop/subscriptiongroups';
	
	public $extensions = [
	    'keyval'=>1, 
	    'attachments'=>1,
	    'changetrack'=>1
	];
	
	public $table = "shop_subscription_groups";
	
		
	
	
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
	
	

	function orderItemPayd($unit_price, $qty, $order)
	{
		$this->fireEvent('BEFORE_CHANGES');
		$this->qty = $this->qty - $qty;
		$this->updateChanged();
	}

	

	
}