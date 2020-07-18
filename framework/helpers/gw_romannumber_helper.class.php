<?php

class GW_RomanNumber_Helper {

	static function decode($roman) {
		$conv = array(
		    array("letter" => 'I', "number" => 1),
		    array("letter" => 'V', "number" => 5),
		    array("letter" => 'X', "number" => 10),
		    array("letter" => 'L', "number" => 50),
		    array("letter" => 'C', "number" => 100),
		    array("letter" => 'D', "number" => 500),
		    array("letter" => 'M', "number" => 1000),
		    array("letter" => 0, "number" => 0)
		);
		$arabic = 0;
		$state = 0;
		$sidx = 0;
		$len = strlen($roman);

		while ($len >= 0) {
			$i = 0;
			$sidx = $len;

			while ($conv[$i]['number'] > 0) {
				if (strtoupper(@$roman[$sidx]) == $conv[$i]['letter']) {
					if ($state > $conv[$i]['number']) {
						$arabic -= $conv[$i]['number'];
					} else {
						$arabic += $conv[$i]['number'];
						$state = $conv[$i]['number'];
					}
				}
				$i++;
			}

			$len--;
		}

		return($arabic);
	}

	static function encode($num, $isUpper = true) {
		$n = intval($num);
		$res = '';

		/*		 * * roman_numerals array ** */
		$roman_numerals = array(
		    'M' => 1000,
		    'CM' => 900,
		    'D' => 500,
		    'CD' => 400,
		    'C' => 100,
		    'XC' => 90,
		    'L' => 50,
		    'XL' => 40,
		    'X' => 10,
		    'IX' => 9,
		    'V' => 5,
		    'IV' => 4,
		    'I' => 1
		);

		foreach ($roman_numerals as $roman => $number) {
			/*			 * * divide to get matches ** */
			$matches = intval($n / $number);

			/*			 * * assign the roman char * $matches ** */
			$res .= str_repeat($roman, $matches);

			/*			 * * substract from the number ** */
			$n = $n % $number;
		}

		/*		 * * return the res ** */
		if ($isUpper)
			return $res;
		else
			return strtolower($res);
	}

}
