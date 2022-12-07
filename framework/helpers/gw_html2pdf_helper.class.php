<?php


/*
 * add 		"dompdf/dompdf": "^0.8.0" 
 * to composer.json
 * and run composer update
 */

use Dompdf\Dompdf;

class GW_html2pdf_Helper
{
	//fontai
	//https://stackoverflow.com/questions/24412203/dompdf-and-set-different-font-family/24517882#24517882
	
	function convert($html, $stream=true, $opts=[])
	{
		///return self::remoteconvert($html, $stream=true, $opts=[]);

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		
			
		$dompdf->set_option("isPhpEnabled", true);
		$dompdf->set_option('enable_font_subsetting', true);
		$dompdf->set_option('defaultFont', 'DejaVu Sans');
		$dompdf->set_option('isRemoteEnabled', true);
		
		if(isset($opts['params'])){
			foreach($opts['params'] as $key => $val)
				$dompdf->set_option( $key, $val );
		}
		
		
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		
		
		
		
		
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		//$dompdf->setPaper('A4', 'landscape');

		
		;	
		
		
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		if($stream)
			$dompdf->stream();		
		else
			return $dompdf->output();
	}
	
	function remoteconvert($html, $stream=true, $opts=[])
	{
		$http=GW_Http_Agent::singleton();
		//you can play directly with last convert
		//http://1.voro.lt:2080/html/dompdf2022/convert.php?idname=last
		
		$result = $http->postRequest("http://1.voro.lt:2080/html/dompdf2022/convert.php", ['html'=>$html, 'options'=>$opts]);
		
		header("Content-Type: application/pdf");
		header('Content-Length: '.strlen( $result ));
		header('Content-Fisposition: inline; filename="' . 'document.pdf' . '"');
		
		echo $result;
		exit;
	}
}


/*
$footer = $pdf->open_object();

  $w = $pdf->get_width();
  $h = $pdf->get_height();

  // Draw a line along the bottom
  $y = $h - 2 * $text_height - 24;
  $pdf->line(16, $y, $w - 16, $y, $color, 1);

  // Add an initals box
  $font = Font_Metrics::get_font("helvetica", "bold");
  $text = "Initials:";
  $color="#000000";
  $width = Font_Metrics::get_text_width($text, $font, $size);
  $pdf->text($w - 16 - $width - 38, $y, $text, $font, $size, $color);
  $pdf->rectangle($w - 16 - 36, $y - 2, 36, $text_height + 4, array(0.5,0.5,0.5), 0.5);

  // Add a logo
  $img_w = 2 * 72; // 2 inches, in points
  $img_h = 1 * 72; // 1 inch, in points -- change these as required
  $pdf->image("print_logo.png", "png", ($w - $img_w) / 2.0, $y - $img_h, $img_w, $img_h);

  // Close the object (stop capture)
  $pdf->close_object();

  // Add the object to every page. You can
  // also specify "odd" or "even"
  $pdf->add_object($footer, "all");
}
 */