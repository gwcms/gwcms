<?php

class d
{

	static $html = Array(
	    "<pre class='hidedebug' style='border-color:",
	    "'>",
	    "</pre>"
	);
	
	static $initcss = "<style> .hidedebug{ background:transparent;margin:5px;border:0;border-left: solid 10px;padding-left:15px;padding:10px 0px 0px 15px }</style>";
	
	static $inithide = "
		<style>

			.hiddendebug{ overflow:hidden; height:25px; cursor:all-scroll; border-bottom: 3px dotted orange !important; }
			.hiddendebug:after { 
				content: '.......';
			    }
		</style>
		<script>
		document.addEventListener('DOMContentLoaded', function(event) { 
			jQueryCode = function(){
			    $('.hidedebug').dblclick(function(){
				$(this).toggleClass('hiddendebug');
			    
			    }).dblclick();
			}

			if(window.jQuery)  jQueryCode();
			else{   
			    var script = document.createElement('script'); 
			    document.head.appendChild(script);  
			    script.type = 'text/javascript';
			    script.src = '//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js';

			    script.onload = jQueryCode;
			}			
			
		});</script>";
		
	
	static function setHide()
	{
		if(self::$inithide)
		{
			echo self::$inithide;
			self::$inithide = null;
		}	
	}
	
	static function initHtml()
	{
		if(self::$initcss)
		{
			echo self::$initcss;
			self::$initcss = null;			
		}
	}
	

	static function ldump($x, $add = '', $color = 'orange')
	{
		if (!headers_sent())
			header('content-type: text/html; charset=UTF-8');

		self::initHtml();
		
		echo self::$html[0] . $color . self::$html[1];
		//debug_print_backtrace();

		if ($x === null)
			$x = '*NULL*';

		if ($x === false)
			$x = '*FALSE*';

		print_r($x);
		echo $add;

		echo self::$html[2];
	}

	static function vdump($x, $add = '', $color = 'orange')
	{
		self::initHtml();
		echo self::$html[0] . $color . self::$html[1];
		var_dump($x);
		echo self::$html[2];
	}

	static function dump($x, $color = 'orange')
	{
		self::ldump($x, self::fbacktrace(debug_backtrace()), $color);
	}

	static function fbacktrace($bt)
	{
		//limit size, sometimes can kill memory
		//$bt = array_slice($bt, 0, 4);


		if (!isset($bt[0]['file'])) //tiesiai is dump funkcijos kviecia
			array_shift($bt);


		//nesitas mane domina	
		if ($bt[0]['file'] == __FILE__ || strpos($bt[0]['file'], 'f.class.php') !== false) //jei kviesta is dumpas funkcijos pirma pointa nuimti
			array_shift($bt);


		//jei kviecia naudojant dump ar dumpas funkcija
		if (!$bt[0]['file'] && $bt[0]['function'] == 'dumpas') //dar vienas zingsnis
			array_shift($bt);

		if ($bt[0]['file'] == __FILE__ || strpos($bt[0]['file'], 'f.class.php') !== false) //ir dar vienas
			array_shift($bt);


		$point1 = $bt[0];
		array_shift($bt);



		$backtracestr = "";
		foreach ($bt as $point) {

			$data = isset($point["args"]) ? print_r($point["args"], true) : false;

			if (strlen($data) > 1000)
				$data = substr($data, 0, 1000) . "...";

			$backtracestr.="<li style='text-decoration:underline;padding:0;margin:0;' title='" . htmlspecialchars($data, ENT_QUOTES) . "'>" . @$point['file'] . " : " . @$point['line'] . " : " . @$point['class'] . @$point['type'] . @$point['function'] . "</li>";
		}

		$GLOBALS['debug_block'] = isset($GLOBALS['debug_block']) ? $GLOBALS['debug_block'] + 1 : 1;

		$str = "\nIm in: <a href='#' onclick='document.getElementById(\"debug_bl_{$GLOBALS['debug_block']}\").style.display=\"block\";this.href=\"\";return false'>" . $point1['file'] . ':' . $point1['line'] . "</a>
		<div id='debug_bl_{$GLOBALS['debug_block']}' style='display:none'><ul>$backtracestr</ul></div>";

		return $str;
	}

	static function dumpas($x)
	{
		self::dump($x);
		exit;
	}

	static function backtrace()
	{
		echo "<pre>";
		debug_print_backtrace();
		echo "</pre>";
	}

	static function jsonNice($array)
	{
		return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
	}

	static function htmlNice($html)
	{
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = FALSE;
		$dom->loadHTML($html);
		$dom->formatOutput = TRUE;

		d::ldump(htmlspecialchars($dom->saveHTML()));
	}

	static function htmldumpas($html)
	{
		d::dumpas(htmlspecialchars($html));
	}
	
	
}
