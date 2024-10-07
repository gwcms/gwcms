<?php


//add here miscellaneous stuff which is hard to say would be used forever or not certain about category, 
//explain where it its used first second and other times,

class GW_Misc_Helper
{

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

}

