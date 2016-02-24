<?php

class GW_String_Helper {

	static $encoding = "UTF-8";

	static function ucfirst($str, $lower_str_end = false) {
		$first_letter = mb_strtoupper(mb_substr($str, 0, 1, self::$encoding), self::$encoding);

		if ($lower_str_end) {
			$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding), self::$encoding);
		} else {
			$str_end = mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding);
		}


		return $first_letter . $str_end;
	}

	static $chars = "ABCDEFGHIJKLMNOPQRSTUWVXYZ0123456789";

	static function getRandString($length) {
		$str = "";
		$cmax = strlen(self::$chars);

		for ($i = 0; $i < $length; $i++) {
			$str.= self::$chars[rand(0, $cmax - 1)];
		}

		return $str;
	}

	function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
		if ($length == 0)
			return '';

		if (mb_strlen($string) > $length) {
			$length -= min($length, mb_strlen($etc));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1));
			}
			if (!$middle) {
				return mb_substr($string, 0, $length) . $etc;
			} else {
				return mb_substr($string, 0, $length / 2) . $etc . mb_substr($string, -$length / 2);
			}
		} else {
			return $string;
		}
	}

}
