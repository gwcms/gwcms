<?php

class GW_FavIco_Tool
{

	public $path_arr;
	public $app;

	function __construct($context)
	{
		$this->path_arr = $context['path_arr'];
	}

	function init()
	{
		//$this->app->initDB();
	}

	function process()
	{
		
		
		$fontsdir=GW::s('DIR/APPLICATIONS').'admin/static/fonts/';
						
	
		$font_file = isset($_GET['font']) ? $fontsdir .($ff=preg_replace("/[^a-z0-9\._-]+/i", '', $_GET['font'])) : false;
			
				
		if(!file_exists($font_file))
			$this->fatalError("No font file $ff");
		
			
		



		$font_size = isset($_GET['fs']) ? $_GET['fs'] : 6;
		$font_color = '#000000';
		$background_color = '#ffffff';
		$transparent_background = true;
		
		$cache_images = isset($_GET['nocache']) ? false: true;
		
		$cache_folder = GW::s('DIR/SYS_IMAGES_CACHE');

		/* ---------------------------------------------------------------------------
		  For basic usage, you should not need to edit anything below this comment.
		  If you need to further customize this script's abilities, make sure you
		  are familiar with PHP and its image handling capabilities.
		  --------------------------------------------------------------------------- */

		$mime_type = 'image/png';
		$extension = '.png';
		$send_buffer_size = 4096;

		// check for GD support
		if (!function_exists('ImageCreate'))
			$this->fatalError('Error: Server does not support PHP image generation');

		// clean up text
		if (empty($_GET['text']))
			$this->fatalError('Error: No text specified.');

		$text = $_GET['text'];
		if (get_magic_quotes_gpc())
			$text = stripslashes($text);
		$text = $this->javascript2html($text);


		if (!empty($_GET['size'])) {
			$font_size = $_GET['size'];
		}
		if (!empty($_GET['color'])) {
			$font_color = $_GET['color'];
		}
		if (!empty($_GET['background'])) {
			$background_color = $_GET['background'];
			
			$transparent_background=false;
		}

		// '0' matches empty
		if (isset($_GET['transparent']) && $_GET['transparent'] == '0') {
			$transparent_background = false;
		}

		// look for cached copy, send if it exists
		$hash = md5(json_encode($_GET));
		$cache_filename = $cache_folder . '/' . 'favico_'.$hash . $extension;
		if ($cache_images && ($file = @fopen($cache_filename, 'rb'))) {
			header('Content-type: ' . $mime_type);
			while (!feof($file))
				print(($buffer = fread($file, $send_buffer_size)));
			fclose($file);
			exit;
		}

		// check font availability
		$font_found = is_readable($font_file);
		if (!$font_found) {
			$this->fatalError('Error: The server is missing the specified font: ' . $font_file);
		}

		// create image
		$background_rgb = $this->hex2rgb($background_color);
		$font_rgb = $this->hex2rgb($font_color);
		$dip = $this->getDip($font_file, $font_size);
		$box = @ImageTTFBBox($font_size, 0, $font_file, $text);


		$height = abs($box[5] - $box[1]);
		$height1 =$height;
		$width1 = abs($box[4]);

		$pad1 = 0;
		
		
		
		

		if (isset($_GET['text2'])) {
			$height = $height * 2 + 2;

			$box2 = @ImageTTFBBox($font_size, 0, $font_file, $_GET['text2']);
			$width2 = abs($box2[4]);
			$width = max($width2, $width1);

			$pad2 = $pad1;

			if ($width1 < $width2)
				$pad1+=round(($width2 - $width1) / 2)-1;

			if ($width2 < $width1)
				$pad2+=round(($width1 - $width2) / 2);
			
			
		}else{
			$width=$width1;
		}
		
		if(isset($_GET['debug'])){
			d::ldump([
				'pad1'=>$pad1, 
				'pad2'=>$pad2,
				'width1'=>$width1,
				'width2'=>$width2,
				'width'=>$width,
				'box'=>$box,
				'box2'=>$box2
				]);
			exit;
		}
		//$this->fatalError("height: $height");

		//$image = @ImageCreate(max($width, $height), max($width, $height));
		$image = @ImageCreate($width, $height);
		


		if (!$image || !$box) {
			$this->fatalError('Error: The server could not create this heading image.');
		}

		// allocate colors and draw text
		$background_color = ImageColorAllocate($image, $background_rgb['red'], $background_rgb['green'], $background_rgb['blue']);
		$font_color = ImageColorAllocate($image, $font_rgb['red'], $font_rgb['green'], $font_rgb['blue']);

		ImageTTFText($image, $font_size, 0, $pad1, $height1-1, $font_color, $font_file, $text);

		if (isset($_GET['text2'])) {
			//ImageTTFText($image, $font_size, 0, $pad2, $height1-1, $font_color, $font_file, $text);

			ImageTTFText($image, $font_size, 0, $pad2, $height - 2, $font_color, $font_file, $_GET['text2']);
		}


		//$this->fatalError("size:".$font_size);
		// set transparency
		if ($transparent_background)
			ImageColorTransparent($image, $background_color);
		
		
		if(isset($_GET['square'])){
			
			$wh=max($width,$height);
			$resized = @ImageCreate($wh, $wh);
			imagecopyresized($resized, $image, 0, 0, 0, 0, $wh, $wh, $width, $height);
			$image=$resized;
			
		}		

		header('Content-type: ' . $mime_type);
		ImagePNG($image);

		// save copy of image for cache
		if ($cache_images) {
			@ImagePNG($image, $cache_filename);
		}

		ImageDestroy($image);
		exit;
	}
	/* try to determine the "dip" (pixels dropped below baseline) of this font for this size. */

	function getDip($font, $size)
	{
		$test_chars = 'abcdefghijklmnopqrstuvwxyz' .
			'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
			'1234567890' .
			'!@#$%^&*()\'"\\/;.,`~<>[]{}-+_-=';
		$box = @ImageTTFBBox($size, 0, $font, $test_chars);
		return $box[3];
	}
	/* attempt to create an image containing the error message given. if this works, the image is sent to the browser. if not, an error is logged, and passed back to the browser as a 500 code instead. */

	function fatalError($message)
	{
		// send an image
		if (function_exists('ImageCreate')) {
			$width = ImageFontWidth(5) * strlen($message) + 10;
			$height = ImageFontHeight(5) + 10;
			if ($image = ImageCreate($width, $height)) {
				$background = ImageColorAllocate($image, 255, 255, 255);
				$text_color = ImageColorAllocate($image, 0, 0, 0);
				ImageString($image, 5, 5, 5, $message, $text_color);
				header('Content-type: image/png');
				ImagePNG($image);
				ImageDestroy($image);
				exit;
			}
		}

		// send 500 code
		header("HTTP/1.0 500 Internal Server Error");
		print($message);
		exit;
	}
	/* decode an HTML hex-code into an array of R,G, and B values. accepts these formats: (case insensitive) #ffffff, ffffff, #fff, fff */

	function hex2rgb($hex)
	{
		// remove '#'
		if (substr($hex, 0, 1) == '#')
			$hex = substr($hex, 1);

		// expand short form ('fff') color
		if (strlen($hex) == 3) {
			$hex = substr($hex, 0, 1) . substr($hex, 0, 1) .
				substr($hex, 1, 1) . substr($hex, 1, 1) .
				substr($hex, 2, 1) . substr($hex, 2, 1);
		}

		if (strlen($hex) != 6)
			$this->fatalError('Error: Invalid color "' . $hex . '"');

		// convert
		$rgb['red'] = hexdec(substr($hex, 0, 2));
		$rgb['green'] = hexdec(substr($hex, 2, 2));
		$rgb['blue'] = hexdec(substr($hex, 4, 2));

		return $rgb;
	}
	/* convert embedded, javascript unicode characters into embedded HTML entities. (e.g. '%u2018' => '&#8216;'). returns the converted string. */

	function javascript2html($text)
	{
		$matches = null;
		preg_match_all('/%u([0-9A-F]{4})/i', $text, $matches);
		if (!empty($matches))
			for ($i = 0; $i < sizeof($matches[0]); $i++)
				$text = str_replace($matches[0][$i], '&#' . hexdec($matches[1][$i]) . ';', $text);
		return $text;
	}
}
