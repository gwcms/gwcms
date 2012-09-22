<?php
include dirname(__DIR__).'/init.php';



require_once 'Image/Barcode.php';
Image_Barcode::draw($_REQUEST['code'], 'code128', 'png');