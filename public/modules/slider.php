<?php

include_once GW::$dir['MODULES'].'dropindesign/did_product.class.php';
$data = new DID_Product();
$request = GW::$smarty->get_template_vars('request');
$itemList = $data->getProductsByCategoryAndType($request->path_arr[0]['name'], $request->path_arr[1]['name']);
$outputStr = '';
foreach ($itemList as $product){
	$img = $product->image;
	if ($img){
		$outputStr.= '<a href='.GW::$request->ln.'/';
		$outputStr.= $request->path_arr[1]['path_clean'] . '/' . $product->id . '"><li><img class="pb-airportexpress" src="tools/img.php?id=' . $img->key . '"></li></a>';
	}
}
GW::$smarty->assign('output', $outputStr);
if(isset($request->path_arr[2])){
	$aP = $data->getById((int)($request->path_arr[2]['name']));
	if(isset($aP[0])){
		$pS = $aP[0]->getProductsByPackage();
		$cS = $aP[0]->getSameTypeProductsByPackage();
		GW::$smarty->assign('activeProduct', $aP[0]);
		GW::$smarty->assign('productSerie', $pS);
		GW::$smarty->assign('colorSerie', $cS);
	}
}		
