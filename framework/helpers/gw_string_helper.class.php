<?php

class GW_String_Helper
{

	static $encoding = "UTF-8";

	static function ucfirst($str, $lower_str_end = false)
	{
		$first_letter = mb_strtoupper(mb_substr($str, 0, 1, self::$encoding), self::$encoding);

		if ($lower_str_end) {
			$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding), self::$encoding);
		} else {
			$str_end = mb_substr($str, 1, mb_strlen($str, self::$encoding), self::$encoding);
		}


		return $first_letter . $str_end;
	}

	static $simple = "ABCDEFGHJKLMNPQRSTUWVXYZ123456789";
	static $chars = "ABCDEFGHIJKLMNOPQRSTUWVXYZabcdefghijklmnopqrstuwxyz0123456789";

	static function getRandString($length, $chars=false)
	{
		$str = "";
		$chars = $chars ?: self::$chars;
		
		$cmax = strlen($chars);

		for ($i = 0; $i < $length; $i++) {
			$str.= $chars[rand(0, $cmax - 1)];
		}

		return $str;
	}

	static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
	{
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
	
	/**
	 * A function to fill the template with variables, returns filled template.
	 * 
	 * @param string $template A template with variables placeholders {$varaible}.
	 * @param array $variables A key => value store of variable names and values.
	 * 
	 * @return string  
	 */

	static function replaceVarsInTpl(&$template, array $variables) {

		$template = preg_replace_callback('#{(.*?)}#', function($match) use ($variables) {
			$match[1] = trim($match[1], '$');
			return $variables[$match[1]];
		}, ' ' . $template . ' ');
	}
	
	static function zero($number, $zerocnt=2)
	{
		return sprintf('%0'.$zerocnt.'d', $number);
	}

	static function split2($string, $center = 0.4) {
		
		$string = str_replace('  ',' ', $string);
		$string = str_replace('  ',' ', $string);
		
		$length2 = strlen($string) * $center;
		$tmp = explode(' ', $string);
		
		if(count($tmp)==2)
			return $tmp;
		
		$index = 0; 
		$result = Array('', '');
		foreach($tmp as $word) {
		    if(!$index && strlen($result[0]) > $length2) $index++;
		    $result[$index] .= $word.' ';
		}
		return $result;
	}
	

	static function applyDiff($diff, $text)
	{
		$dmp = new \DiffMatchPatch\DiffMatchPatch();
		$patches = $dmp->patch_fromText($diff);
		
		//d::dumpas([$text, $diff]);
		
		$result = $dmp->patch_apply($patches, $text);

		return $result[0];
	}

	static function createDiff($text1, $text2)
	{
		//php7.4 /var/www/gw/gwcms/composer.phar require yetanotherape/diff-match-patch
		$dmp = new \DiffMatchPatch\DiffMatchPatch();
		$patches = $dmp->patch_make($text1, $text2);
		return $dmp->patch_toText($patches);
		
	}
}

