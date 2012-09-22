{php}
	include_once GW::$dir['MODULES'].'dropindesign/did_product.class.php';
	$data = new DID_Product();
	$data->id = ((int)GW::$request->path_arr[2]['name']);
	$prod_item = $data->getInfo();
	GW::$smarty->assign('prod_item', $prod_item);
{/php}

<div class="kundeflash">
	<object height="600" width="960" align="middle" id="flashfile">
	{$price = $prod_item->price + $prod_item->mod_price - $prod_item->red_price}
	<param value="product_id={$request->path_arr[2]['name']}&product_price={$price}&two_sided={!$prod_item->folded}" name="FlashVars">
	<param value="noscale" name="scale">
	<embed height="600" width="978" scale="noscale" type="application/x-shockwave-flash" flashvars="product_id={$request->path_arr[2]['name']}&product_price={$price}&two_sided={!$prod_item->folded}" src="kundeflash/Kundeflash-debug/Kundeflash.swf">


</object>
</div>
</div>