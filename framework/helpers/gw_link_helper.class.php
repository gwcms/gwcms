<?php


class GW_Link_Helper
{
	static function __trackingLink($match)
	{
		return '%%TRACKINGLINK%%'.  base64_encode($match[0]);
	}
	
	static function trackingLink($text) 
	{
		$rexProtocol = '(https?://)';
		$rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
		$rexPort     = '(:[0-9]{1,5})?';
		$rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
		$rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';

		return preg_replace_callback("&$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment&",
		    "self::__trackingLink", $text);
	}
	
	
	
	static function __parseLinksHTML($match)
	{
	    // Prepend http:// if no protocol specified
	    $completeUrl = $match[1] ? $match[0] : "http://{$match[0]}";

	    return '<a target="_blank" href="' . $completeUrl . '">'
		. $match[2] . $match[3] . $match[4] . '</a>';
	}	

	static function parse($text) 
	{
		$rexProtocol = '(https?://)?';
		$rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
		$rexPort     = '(:[0-9]{1,5})?';
		$rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
		$rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
		$rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';

		return preg_replace_callback("&\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))&",
		    "self::__parseLinksHTML", $text);
	}

}