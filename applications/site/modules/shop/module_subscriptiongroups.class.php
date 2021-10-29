<?php

class Module_Subscriptiongroups extends GW_Public_Module
{
	var $view_path_index=1;
	

	
				

	/**
	 * to be expanded in cart view
	 */
	public $cart_items = [];
	
	/**
	 *
	 * @var Nat_Order
	 */
	public $order ;
	
	function init()
	{
		
		parent::init();
		$this->config = new GW_Config($this->module_path[0] . '/');
		$this->config->preload('');	
		
		
		
		$this->tpl_dir .= $this->module_name."/";

		
		
		
		$this->model = new Shop_SubscriptionGroups();
				
		$this->paging_enabled=1;
		


		
	}
	
	
	function viewDefault()
	{
		
		$list = $this->model->findAll('active=1');
		
		$this->tpl_vars['list'] = $list;
	}
	
	function viewGroup()
	{
		
		$item = $this->model->find(['id=? AND active=1', $_GET['id']]);
		
		$this->tpl_vars['item'] = $item;
	}
	


	
	function doAdd2Cart()
	{
		$this->userRequired();
		$cartvals = $_REQUEST['item'];
		
		
		
		$item = Shop_Products::singleton()->find(['id=? AND active=1', $cartvals['id']]);
		
		
		
		$cart = $this->app->user->getCart(true);
	
		
		$payprice = $item->price;
		

		//nebepridet pakartotinai
		$cartitem = $cart->getItem(Shop_Products::singleton()->createNewObject($cartvals['id'])) ?: new GW_Order_Item;
		
		
		$url = $this->app->buildUri('direct/shop/shop/p',['id'=>$item->id]);
		
		
		$vals = [
			'obj_type'=>'shop_products',
			'obj_id'=>$item->id,
			'qty' => min($cartitem->qty + ($cartvals['qty'] ?? 1), $item->qty),
			'unit_price'=>$payprice,
			//'context_obj_id'=>$user->id,
			//'context_obj_type'=>'gw_customer'
			'qty_range'=>$this->feat('cart_item_qty1') ? "" :  "1;$item->qty",
			'deliverable'=>$this->feat('delivery') ? 10 : 0, //real item
			'link' =>$url
		];
		
		//modifikacijoms
		if($item->parent_id){
			$vals['context_obj_type'] = 'shop_products';
			$vals['context_obj_id'] = $item->parent_id;
		}
		
		$cartitem->setValues($vals);
		
		$cart->addItem($cartitem);
		
		
		
		
		$this->setMessage([
		    'text'=>'<b>'.$item->title.'</b> '.GW::ln('/M/SHOP/ADDED_TO').' '.GW::ln('/M/SHOP/CART', ['l'=>'gal','c'=>1]),
		    'type'=>0,
		    'buttons'=>[['title'=>'<i class="fa fa-shopping-cart"></i> '.GW::ln('/m/VIEW_CART'), 'url'=>$this->app->buildUri('direct/orders/orders/cart')]]
			]);
		
		
		//d::dumpas('aaa');
		$args = $_GET;
		unset($args['act']);
		$this->app->jump(false, $args);
		
	}	

	

		
	
}