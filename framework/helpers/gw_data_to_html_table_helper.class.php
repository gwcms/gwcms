<?php

class GW_Data_to_Html_Table_Helper
{

	static function doTable($data, $font_size = 10)
	{
		if (!is_array($data))
			return;

		$str = "";
		$str.= "<table class='gwTable'>";
		$str.="<tr><th>no</th>";
		foreach (array_keys((array) current($data)) as $key)
			$str.="<th>" . htmlspecialchars($key) . "</th>";
		$str.="</tr>\n";

		foreach ($data as $i => $row) {
			$str.="<tr><td>" . $i . "</td>";
			foreach ($row as $field => $val)
				$str.="<td>" . htmlspecialchars(is_array($val) ? implode(", \n", $val) : $val) . "</td>";
			$str.="</tr>\n";
		}
		$str.="</table>";
		return $str;
	}
}
