<?php

class GW_Math_Helper
{

	static function cFileSize($bytes, $prec = 2)
	{
		if (!$bytes)
			return '0';
		$m = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
		$exp = floor(log($bytes) / log(1024));
		$prec = pow(10, $prec);
		return (round($bytes / pow(1024, floor($exp)) * $prec) / $prec) . ' ' . $m[$exp] . 'B';
	}
	/*
	  uptime funkcija
	  precision M-menesiais d-dienomis h-valandomis  m-minutemis s-sekundemis
	 */

	static function uptime($secs, $precision = 's')
	{

		$y = floor($secs / 31514400);
		$secs-=$y * 31514400;
		$M = floor($secs / 2592000);
		$secs-=$M * 2592000;
		$d = floor($secs / 86400);
		$secs-=$d * 86400;
		$h = floor($secs / 3600);
		$secs-=$h * 3600;
		$m = floor($secs / 60);
		$secs-=$m * 60;
		$s = $secs;

		$y = ($y ? $y . 'Y ' : '');
		$M = ($M ? $M . 'M ' : '');
		$d = ($d ? $d . 'd ' : '');
		$h = ($h ? $h . 'h ' : '');
		$m = ($m ? $m . 'm ' : '');
		$s = ($s ? $s . 's ' : '');

		$t = $y;


		if (is_numeric($precision)) {
			$ta = Array($y, $M, $d, $h, $m, $s);

			foreach ($ta as $offset => $te)
				if ($te)
					break;

			$ta = array_slice($ta, $offset, $precision);
			$t = '';

			foreach ($ta as $te)
				$t.=$te;
		}else {
			switch ($precision) {
				case 'M':$t.=$M;
					if ($M)
						break;;
				case 'd':$t.=$M . $d;
					if ($d)
						break;;
				case 'h':$t.=$M . $d . $h;
					if ($h)
						break;;
				case 'm':$t.=$M . $d . $h . $m;
					if ($m)
						break;;
				case 's':$t.=$M . $d . $h . $m . $s;
					if ($s)
						break;;
				case '1':
					if ($M) {
						return $M;
					} elseif ($d) {
						return $d;
					} elseif ($h) {
						return $h;
					} elseif ($m) {
						return $m;
					} elseif ($s) {
						return $s;
					}
					break;
				case '2':
					if ($M) {
						return $M . $d;
					} elseif ($d) {
						return $d . $h;
					} elseif ($h) {
						return $h . $m;
					} elseif ($m) {
						return $m . $s;
					} elseif ($s) {
						return $s;
					}
					break;
			}
		}

		return substr($t, 0, -1);
	}

	static function uptimeDate($datetimestr, $precision = 's')
	{
		return self::uptime(time() - strtotime($datetimestr), $precision);
	}

	function uptimeReverse($str)
	{
		$time = 0;

		if (preg_match('/(\d+) ?h/', $str, $m))
			$time+=$m[1] * 3600;
		if (preg_match('/(\d+) ?m/', $str, $m))
			$time+=$m[1] * 60;
		if (preg_match('/(\d+) ?s/', $str, $m))
			$time+=$m[1];
		if (preg_match('/(\d+) ?d/', $str, $m))
			$time+=$m[1] * 86400;
		if (preg_match('/(\d+) ?M/', $str, $m))
			$time+=$m[1] * 2592000;
		if (preg_match('/(\d+) ?Y/', $str, $m))
			$time+=$m[1] * 31514400;

		return $time;
	}
}
