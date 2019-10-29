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
			'tys' => 'čiui',
			'dys' => 'džiui',
			'ys' => 'iui'		
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
	static function getName ( $vardas, $linksnis = 'sau' ) 
	{
		$vardai = explode( ' ', self::sanitizeName($vardas) );
		$vardaiL = [];
		foreach ( $vardai as $v ) {
			$vardaiL[] = self::getLinksnis( $v, $linksnis );
		}
		
		return count($vardaiL) ? implode(' ', $vardaiL) : $vardas;
	}
	
	/**
	* Vardų sanitarija
	*
	* @param string $vardas lietuviškas vardas arba pavardė
	* @return string
	*/
	static function sanitizeName ( $vardas ) 
	{
		$vardas = mb_eregi_replace('[^a-ž]', ' ', $vardas);
		$vardas = mb_eregi_replace('\s+', ' ', $vardas);
		$vardas = trim($vardas);
		$vardas = mb_convert_case($vardas, MB_CASE_TITLE);
		
		return $vardas;
	
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
		$return = $vardas;

		foreach ( self::$map[$linksnis] as $from=>$to ) {
			if ( mb_substr( $return, -mb_strlen($from) ) == $from ) {
				$return = mb_substr( $return, 0, -mb_strlen($from) );
				$return .= $to;
				break;
			}
		}
				
		return $return;
	}
}

