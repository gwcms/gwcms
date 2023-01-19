<?php

class d
{
	
	static $initcss = "<style> .debugblock{ background:white;margin:5px;border:0;border-left: solid 10px;padding-left:15px;padding:10px 0px 0px 15px }</style>";
	
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
			    $('.debugblock').dblclick(function(){
				$(this).toggleClass('hiddendebug');
			    
			    })
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
	
	static $initHideAll = "<script>$(function(){ $('.debugblock').addClass('hiddendebug'); })</script>";
		
	
	static function setAllHide()
	{
		self::setHide();
			
		if(self::$inithideAll)
		{
			echo self::$inithideAll;
			self::$inithideAll = null;
		}	
	}
	
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
		if(self::$initcss && !isset($_SERVER['SHELL']))
		{
			echo self::$initcss;
			self::$initcss = null;			
		}
	}
	

	static function ldump($x, $opts=[])
	{
		$color = $opts['color'] ?? 'orange';
		$add = $opts['color'] ?? '';
		$hidden = $opts['hidden'] ?? false;
		$output = $opts['output'] ?? 'print_r';
		
		if (!headers_sent())
			header('content-type: text/html; charset=UTF-8');

		self::initHtml();
	    
	   	if($hidden)
			self::setHide();
		
		
		if(isset($_SERVER['SHELL'])){
			echo "------------------\n";
		}else{
			echo "<pre class='debugblock ".($hidden?'hiddendebug':'')."' style='border-color:" . $color . "'>";
		}
		//debug_print_backtrace();
	   	if($hidden)
		{
			self::setHide();
			echo "<span style='color:silver'>Collapsed block: </span><b>$hidden</b></br>";
		}		
		

		if ($x === null)
			$x = '*NULL*';

		if ($x === false)
			$x = '*FALSE*';


		switch($output){
			case 'print_r':
				print_r($x);
			break;
			case 'var_dump':
				var_dump($x);
			break;		
		}
		
		
		
		echo $add;
		
		if(isset($opts['backtrace']))
			echo self::fbacktrace(debug_backtrace());
		
		if(isset($_SERVER['SHELL'])){
			echo "------------------\n";
		}else{
			echo  "</pre>";
		}
		
		if(isset($opts['kill']))
			exit;
	}

	static function vdump($x, $opts=[])
	{		
		$opts['output']="var_dump";
		d::ldump($x, $opts);
	}

	static function dump($x, $opts=[])
	{
		$opts['backtrace']=1;
		self::ldump($x, $opts);
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

			$backtracestr.="<li class='openfile' style='text-decoration:underline;padding:0;margin:0;' title='" . htmlspecialchars($data, ENT_QUOTES) . "'>" . @$point['file'] . " : " . @$point['line'] . " : " . @$point['class'] . @$point['type'] . @$point['function'] . "</li>";
		}

		$GLOBALS['debug_block'] = isset($GLOBALS['debug_block']) ? $GLOBALS['debug_block'] + 1 : 1;

		if(isset($_SERVER['SHELL'])){
			$str = "Im in {$point1['file']} {$point1['line']}\n";
		}else{
			$str = "\nIm in: <a href='#' onclick='document.getElementById(\"debug_bl_{$GLOBALS['debug_block']}\").style.display=\"block\";this.href=\"\";return false'>" . $point1['file'] . ':' . $point1['line'] . "</a>
			<div id='debug_bl_{$GLOBALS['debug_block']}' style='display:none'><ul>$backtracestr</ul></div>";
			
			GW_Debug_Helper::openInNetBeans();
		}
		

		return $str;
	}

	static function dumpas($x, $opts=[])
	{
		//echo "<pre>";
		//var_dump([GW::$context->app->user]);
		/*
		if(GW::$context->app && GW::$context->app->app_name=='SITE' && GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD && 
			(!GW::$context->app->user || !GW::$context->app->user->isRoot())){
			if(isset($_SERVER['SHELL'])){
				echo "------------------\n";
			}else{
				echo "<span style='color:red' title='Test dot'>.</span>";
			}
			
			return false;
		}*/
		
		$opts['kill']=1;
		self::dump($x, $opts);
		
	}

	static function backtrace()
	{
		echo "<pre>";
		debug_print_backtrace();
		echo "</pre>";
	}

	static function jsonNice($array)
	{
		return json_encode($array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
