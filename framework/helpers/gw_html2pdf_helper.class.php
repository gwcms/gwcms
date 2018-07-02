<?php


/*
 * add 		"dompdf/dompdf": "^0.8.0" 
 * to composer.json
 * and run composer update
 */

use Dompdf\Dompdf;

class GW_html2pdf_Helper
{
	function convert($html, $stream=true)
	{


		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		
			
		$dompdf->set_option('enable_font_subsetting', true);
		$dompdf->set_option('defaultFont', 'Arial');
		$dompdf->set_option('isRemoteEnabled', true);
		
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
}
