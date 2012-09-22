{if !GW::$user}
JUMP		
{/if}
{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
{php}
	include_once GW::$dir['MODULES'].'dropindesign/did_product.class.php';
	$data = new DID_Product();
	$item = GW::$smarty->get_template_vars('item');
	$data->id = $item->product_id;
	$prod_item = $data->getInfo();
	GW::$smarty->assign('prod_item', $prod_item);
{/php}
{$price = $prod_item->price + $prod_item->mod_price - $prod_item->red_price}
	<div class="kundeflash">
		<object height="600" width="960" align="middle" id="flashfile">
		<param value="product_id={$item->product_id}&user_product_id={$item->id}&product_price={$price}&two_sided={!$prod_item->folded}" name="FlashVars">
		<param value="noscale" name="scale">
		<embed height="600" width="978" scale="noscale" type="application/x-shockwave-flash" flashvars="product_id={$item->product_id}&user_product_id={$item->id}&product_price={$price}&two_sided={!$prod_item->folded}" src="kundeflash/Kundeflash-debug/Kundeflash.swf">
		</object>
	</div>
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}