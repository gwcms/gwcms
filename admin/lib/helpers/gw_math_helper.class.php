<?php

class GW_Math_Helper
{

	function cFileSize($bytes,$prec=2)
	{
		if(!$bytes) return '0';
		$m = array('','K','M','G','T','P','E','Z','Y');
		$exp = floor(log($bytes)/log(1024));
		$prec=pow(10,$prec);
		return (round($bytes/pow(1024,floor($exp)) * $prec) / $prec) .' '.$m[$exp].'B';
	}

	/*
	uptime funkcija
	precision M-menesiais d-dienomis h-valandomis  m-minutemis s-sekundemis
	*/
		
	function uptime($secs,$precision='s')
	{
		$y=floor($secs/31514400);$secs-=$y*31514400;
		$M=floor($secs/2592000);$secs-=$M*2592000;
		$d=floor($secs/86400);$secs-=$d*86400;
		$h=floor($secs/3600);$secs-=$h*3600;
		$m=floor($secs/60);$secs-=$m*60;
		$s=$secs;	
		
		$y=($y?$y.' y. ':'');
		$M=($M?$M.' m. ':'');
		$d=($d?$d.' d. ':'');
		$h=($h?$h.' h. ':'');
		$m=($m?$m.' m. ':'');
		$s=($s?$s.' s. ':'');
		
		$t=$y;
		
		
		if(is_numeric($precision)){
			$ta=Array($y,$M,$d,$h,$m,$s);
			
			foreach($ta as $offset => $te)
				if($te)break;
			
			$ta = array_slice($ta,$offset,$precision);
			$t='';
			
			foreach($ta as $te)
				$t.=$te;
		}else{
			switch($precision){
				case 'M':$t.=$M;             if($M)break;;
				case 'd':$t.=$M.$d;          if($d)break;;
				case 'h':$t.=$M.$d.$h;       if($h)break;;
				case 'm':$t.=$M.$d.$h.$m;    if($m)break;;
				case 's':$t.=$M.$d.$h.$m.$s; if($s)break;; 
			}
		}
		
		return substr($t,0,-1);
	}	
}