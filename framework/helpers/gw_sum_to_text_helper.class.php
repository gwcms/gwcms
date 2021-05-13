<?php

class GW_Sum_To_Text_Helper {

	
	static function sum2Text($number, $lang='lt')
	{
		$Sum = sprintf('%01.2f', $number);
		list($p1, $p2) = explode('.', $Sum);		
		
		$f = new NumberFormatter($lang, NumberFormatter::SPELLOUT);
		$spell=$f->format($p1);
		
		return $spell . ' ' . self::{$lang=='lt'?'getCurrencyLT':'getCurrencyEN'}($p1) . ' ' . $p2 . ' cnt.';
	}
	
	
	
	
	static function sum2TextLT($skaicius) {

		$Sum = sprintf('%01.2f', $skaicius);
		list($p1, $p2) = explode('.', $Sum);
		$SumZodziais = self::getSumZodziais($p1) . ' ' . self::getCurrencyLT($p1) . ' ' . $p2 . ' cnt.';
		return $SumZodziais;
	}

	static function getCurrencyLT($number, $begin = 'eur') {
		if ($number == 0)
			return $begin . 'ų';

		$last = substr($number, -1);
		$du = substr($number, -2, 2);

		if (($du > 10) && ($du < 20))
			return $begin . 'ų';
		else {
			if ($last == 0)
				return $begin . 'ų';
			elseif ($last == 1)
				return $begin . 'as';
			else
				return $begin . 'ai';
		}
	}
	
	static function getCurrencyEN($number, $begin = 'eur') {
		return $number > 1 ? $begin.'os' : $begin.'o';
	}	

	static function getTrys($skaicius) 
	{
		$vienetai = array('', 'vienas', 'du', 'trys', 'keturi', 'penki', 'šeši', 'septyni', 'aštuoni', 'devyni');
		$niolikai = array('', 'vienuolika', 'dvylika', 'trylika', 'keturiolika', 'penkiolika', 'šešiolika', 'septyniolika', 'aštuoniolika', 'devyniolika');
		$desimtys = array('', 'dešimt', 'dvidešimt', 'trisdešimt', 'keturiasdešimt', 'penkiasdešimt', 'šešiasdešimt', 'septyniasdešimt', 'aštuoniasdešimt', 'devyniasdešimt');

		$skaicius = sprintf("%03d", $skaicius);
		$simtai = ($skaicius[0] == 1) ? "šimtas" : "šimtai";
		if ($skaicius[0] == 0)
			$simtai = "";

		$du = substr($skaicius, 1);
		if (($du > 10) && ($du < 20))
			return self::getSumZodziais($skaicius[0] . "00") . " " . $niolikai[$du[1]];
		else
			return $vienetai[$skaicius[0]] . " " . $simtai . " " . $desimtys[$skaicius[1]] . " " . $vienetai[$skaicius[2]];
	}

	static function getSumZodziais($skaicius) {
		$zodis = array(
		    array("", "", ""),
		    array("tūkstančių", "tūkstantis", "tūkstančiai"),
		    array("milijonų", "milijonas", "milijonai"),
		    array("milijardų", "milijardas", "milijardai"),
		    array("bilijonų", "bilijonas", "bilijonai"));

		$return = "";
		if ($skaicius == 0)
			return "nulis";

		settype($skaicius, "string");
		$size = strlen($skaicius);
		$skaicius = str_pad($skaicius, ceil($size / 3) * 3, "0", STR_PAD_LEFT);

		for ($ii = 0; $ii < $size; $ii+=3) {
			$tmp = substr($skaicius, 0 - $ii - 3, 3);
			$return = self::getTrys($tmp) . " " . $zodis[$ii / 3][($tmp[2] > 1) ? 2 : $tmp[2]] . " " . $return;
		}
		return $return;
	}

	static function getCentus($skaicius) {
		$centai = explode('.', $skaicius);
		return $centai[1];
	}

}
