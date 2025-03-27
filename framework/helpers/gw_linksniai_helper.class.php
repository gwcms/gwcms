<?php

class GW_Linksniai_Helper 
{
	
	
	static $map = [
	    // kilmininkas (ko?)
	    'kil'=>[
			'a' => 'os',
			'as' => 'o',
			'ė' => 'ės',
			'tis' => 'čio',
			'dis' => 'džio',
			'is' => 'io',
			'us' => 'aus',
			'tys' => 'čio',
			'dys' => 'džio',
			'ys' => 'io'
		],
	    // naudininkas (kam?)
	    'nau'=>[
			'a' => 'ai',
			'as' => 'ui',
			'ė' => 'ei',
			'tis' => 'čiui',
			'dis' => 'džiui',
			'is' => 'iui',
			'us' => 'ui',
			'ius' => 'iui',
			'tys' => 'čiui',
			'dys' => 'džiui',
			'ys' => 'iui',
			'ia' => 'iai'
	    ],
	    // galininkas (ką?)
		'gal'=>[
			'a' => 'ą',
			'as' => 'ą',
			'ė' => 'ę',
			'is' => 'į',
			'us' => 'ų',
			'ys' => 'į'		    
		],
	    // įnagininkas (kuo?)
		'ina'=>[
		    	'a' => 'a',
			'as' => 'u',
			'ė' => 'e',
			'tis' => 'čiu',
			'dis' => 'džiu',
			'is' => 'iu',
			'us' => 'u',
			'tys' => 'čiu',
			'dys' => 'džiu',
			'ys' => 'iu'
		],
	    // vietininkas (kur? kame?)
		'vie'=>[
			'a' => 'oje',
			'as' => 'e',
			'ė' => 'ėje',
			'is' => 'yje',
			'us' => 'uje',
			'ys' => 'yje'		    
		],
	    // šauksmininkas
		'sau'=>[
			'a' => 'a',
			'as' => 'ai',
			'ė' => 'e',
			'is' => 'i',
			'us' => 'au',
			'ys' => 'y'		    
		]
	];
	
	static $balses='euioaąęėįųū';
		
	function __construct ( $encoding = 'UTF-8' ) 
	{
		mb_internal_encoding($encoding) ;
	}
	
	/**
	* Vardų transformacija
	*
	* @param string $vardas lietuviškas vardas arba pavardė
	* @param string $linksnis sutrumpintas linksnio pavadinimas: kil, nau, gal, ina, vie, sau
	* @return string
	*/
	static function getName ( $vardas, $linksnis = 'sau') 
	{
		$vardai = preg_split( '/[ -]/', self::sanitizeName($vardas) );
		
		//d::ldump([$linksnis]);
		
		
		$vardaiL = [];
		foreach ( $vardai as $v ) {
			if(mb_strlen($v)<3)
				continue;
			
			$replacelimit = 1;
			$vardas = str_ireplace($v,self::getLinksnis( $v, $linksnis ) , $vardas, $replacelimit);
		}
		
		return $vardas;
	}
	
	/**
	* Vardų sanitarija
	*
	* @param string $vardas lietuviškas vardas arba pavardė
	* @return string
	*/
	static function sanitizeName($vardas) 
	{
		// Replace all non-Lithuanian letters with a space
		$vard = preg_replace('/[^a-ž]/iu', ' ', $vardas);

		// Replace multiple spaces with a single space
		$vard = preg_replace('/\s+/u', ' ', $vard);

		// Trim spaces
		$vard = trim($vard);

		// Convert to title case (if needed)
		// $vard = mb_convert_case($vard, MB_CASE_TITLE, "UTF-8");

		return $vard;
	}
	
	static function arbalse($raide)
	{
		return strpos(self::$balses, $raide)!==false;
	}

	/**
	* Vardas linksnyje
	*
	* @param string $vardas lietuviškas vardas arba pavardė
	* @param string $linksnis sutrumpintas linksnio pavadinimas: kil, nau, gal, ina, vie, sau
	* @return string
	*/
	static function getLinksnis ( $vardas, $linksnis = 'sau' ) 
	{
		$return = trim($vardas);

		foreach ( self::$map[$linksnis] as $from=>$to ) {
			
			//echo "<span style='font-size:3mm'>";
			//d::ldump(['vard'=>$vardas, 'l'=>$linksnis, 'from'=>$from, 'pirmafrom'=>mb_substr( $from, 0, 1)]);
			//echo '</font>';
			
			if(self::arbalse(mb_substr( $return, -mb_strlen($from)-1,1)) && self::arbalse(mb_substr( $from, 0, 1)) )
				continue;
				
			if ( mb_substr( $return, -mb_strlen($from) ) == $from ) {
				$return = mb_substr( $return, 0, -mb_strlen($from) );
				$return .= $to;
				break;
			}
		}
				
		return $return;
	}
}

