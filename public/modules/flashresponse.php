<?php

include GW::$dir['MODULES'].'/dropindesign/did_image.class.php';
$data = new DID_Image();
$liste = $data->getImagesByProductId(GW::$request->path_arr[1]['name']);
$antall = 0;
foreach ($liste as $element){
	$antall +=1;
}

GW::$smarty->assign('nr', $antall);
GW::$smarty->assign('imageListe', $liste);
