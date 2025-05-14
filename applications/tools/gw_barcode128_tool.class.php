<?php

class GW_BarCode128_Tool
{
	public $path_arr;
	
	public $admin=false;
	
	public $app;
	
	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
		
	}
	
	
	function init()
	{
		$this->app->initDB();
	}
	
	function process()
	{
		/*
		set_include_path(GW::s('DIR/PEAR'));
		require_once GW::s('DIR/PEAR').'Image/Barcode.php';
		$imbc = new Image_Barcode;
		$imbc->draw($_REQUEST['code'], 'code128', 'png');
		*/
		

		require 'vendor/autoload.php';

		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		header('Content-type: image/png');
		$barcode = $generator->getBarcode($code=$_REQUEST['code'], $generator::TYPE_CODE_128);		
		
		// Create image from barcode
		$barcodeImage = imagecreatefromstring($barcode);

		// Get barcode dimensions
		$width = imagesx($barcodeImage);
		$height = imagesy($barcodeImage);

		// Allocate space for text below
		$fontHeight = 12;
		$textMargin = 5;
		$totalHeight = $height + $fontHeight + $textMargin;

		$finalImage = imagecreatetruecolor($width, $totalHeight);
		$white = imagecolorallocate($finalImage, 255, 255, 255);
		$black = imagecolorallocate($finalImage, 0, 0, 0);

		imagefilledrectangle($finalImage, 0, 0, $width, $totalHeight, $white);

		// Copy barcode into new image
		imagecopy($finalImage, $barcodeImage, 0, 0, 0, 0, $width, $height);

		// Write text
		$fontsdir=GW::s('DIR/APPLICATIONS').'admin/static/fonts/';
		$font = $fontsdir . '/Arial-Bold.ttf'; // Path to a TTF font file


		if (file_exists($font)) {
		    imagettftext($finalImage, 10, 0, 10, $height + $fontHeight, $black, $font, $code);
		} else {
		    imagestring($finalImage, 3, 10, $height + 2, $code, $black);
		}

		// Output final image
		header('Content-Type: image/png');
		//header('Content-Type: text/plain');
		imagepng($finalImage);
		imagedestroy($finalImage);		
		
		
	}
}