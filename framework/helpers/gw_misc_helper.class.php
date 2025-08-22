<?php

//add here miscellaneous stuff which is hard to say would be used forever or not certain about category, 
//explain where it its used first second and other times,

class GW_Misc_Helper {

	static $encoding = "UTF-8";

	//robotika sukeist varda su pavarde

	static function switchNameSurname($input) {
		// Remove multiple spaces and trim the input
		$input = preg_replace('/\s+/', ' ', trim($input));

		// Split the input into parts (name and surname)
		$parts = explode(' ', $input);

		// If there's only one part, return as is
		if (count($parts) == 1) {
			return $input;
		}

		// Extract the surname (last part) and the rest as names
		$surname = array_pop($parts);
		$names = implode(' ', $parts);

		// Return the result as "surname names"
		return $surname . ' ' . $names;
	}


	static function fetchMetaTags($url) {
		// Initialize result array
		$result = [
		    'title' => null,
		    'description' => null,
		    'icon' => null,
		    'image' => null,
		];

		// Fetch the HTML content
		$html = @file_get_contents($url);
		if ($html === false) {
			return ['error' => 'Unable to fetch URL content.'];
		}

		// Convert HTML to UTF-8 if not already in UTF-8
		$encoding = mb_detect_encoding($html, 'UTF-8, ISO-8859-1, ISO-8859-15', true);
		if ($encoding !== 'UTF-8') {
			$html = mb_convert_encoding($html, 'UTF-8', $encoding);
		}

		// Add UTF-8 meta tag to ensure proper parsing
		$html = preg_replace('/<head>/i', '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">', $html);

		// Load the HTML into DOMDocument
		$dom = new DOMDocument();
		@$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

		// Retrieve the title
		$titleElements = $dom->getElementsByTagName('title');
		if ($titleElements->length > 0) {
			$result['title'] = $titleElements->item(0)->nodeValue;
		}

		// Retrieve meta tags
		$metaTags = $dom->getElementsByTagName('meta');
		foreach ($metaTags as $meta) {
			if ($meta->getAttribute('name') === 'description') {
				$result['description'] = $meta->getAttribute('content');
			}
			if ($meta->getAttribute('property') === 'og:image') {
				$result['image'] = $meta->getAttribute('content');
			}
		}

		// Retrieve the site icon
		$linkTags = $dom->getElementsByTagName('link');
		foreach ($linkTags as $link) {
			if ($link->getAttribute('rel') === 'icon' || $link->getAttribute('rel') === 'shortcut icon') {
				$result['icon'] = $link->getAttribute('href');
			}
		}

		// Fix relative URLs for icon and image
		$parsedUrl = parse_url($url);
		$base = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

		if (!empty($result['icon']) && !parse_url($result['icon'], PHP_URL_SCHEME)) {
			$result['icon'] = $base . '/' . ltrim($result['icon'], '/');
		}

		if (!empty($result['image']) && !parse_url($result['image'], PHP_URL_SCHEME)) {
			$result['image'] = $base . '/' . ltrim($result['image'], '/');
		}

		return $result;
	}
	
	
	static function extractNameSurname($input) {
		// Remove multiple spaces and trim the input
		$input = preg_replace('/\s+/', ' ', trim($input));

		// Split the input into parts (name and surname)
		$parts = explode(' ', $input);

		// If there's only one part, return as is
		if (count($parts) == 1) {
		    return $input;
		}

		// Extract the surname (last part) and the rest as names
		$surname = array_pop($parts);
		$names = implode(' ', $parts);

		// Return the result as "surname names"
		return [$names, $surname];
	}	
	
	static function extractSurname($input) {
		$tmp = self::extractNameSurname($input);
		
		return is_array($tmp) ? $tmp[1] : $tmp;
	}		

}
