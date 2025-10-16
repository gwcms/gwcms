<?php

class GW_Data_to_Html_Table_Helper
{

	static function doTable($data, array $opts=[])
	{
		$escape = $opts['escape'] ?? 1;
		$mergegroup = $opts['mergegroup'] ?? 0;
		
		if (!is_array($data))
			return;

		$keys = array_keys((array) current($data));
		
		$str = "";
		$str.= "<table class='gwTable'>";
		$str.="<tr><th>no</th>";
		foreach ($keys as $key)
			$str.="<th>" . htmlspecialchars($key) . "</th>";
		$str.="</tr>\n";
		
		$merge = [];
		
		if($mergegroup){
			$lastval = null;
			
			foreach($keys as $field)
				foreach($data as $idx => $row){
					if($row[$field]!=$lastval){
						$startidx = $idx;
						$lastval = $row[$field];
					}else{
						@$merge[$field][$startidx]++;
					}
				}
			//d::dumpas($merge);
		}

		$rowspanskip = [];
		
		foreach ($data as $i => $row) {
			$str.="<tr><td>" . $i . "</td>";
			foreach ($row as $field => $val){
				$val = is_array($val) ? implode(", \n", $val) :  $val;
				
				if($escape)
					$val =  htmlspecialchars((string)$val);
				
				if(isset($merge[$field][$i])){
					$rowspan="rowspan='".($merge[$field][$i]+1)."'";
					$rowspanskip[$field]=$merge[$field][$i];
				}elseif($rowspanskip[$field] ?? false > 0){
					$rowspanskip[$field]--;
					continue;;
				}else{
					$rowspan="";
				}
				
					
		
				
				$str.="<td $rowspan>" . str_replace("\n", "<br />",  $val).'</td>';
			}
			$str.="</tr>\n";
		}
		$str.="</table>";
		return $str;
	}
	
	static function doTableSingleRecord($data, $opts=[])
	{
		if (!is_array($data))
			return;

		$str = "";
		$str.= "<table class='gwTable'>";
		
		
		foreach($data as $fieldname => $value)
		{
			if(is_array($value) || is_object($value))
				$value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				
			switch($opts['valformat'][$fieldname] ?? 1){
				case 1:
					$value = str_replace("\n",'<br>',htmlspecialchars($value));
				break;
				case 2:
					$value = htmlspecialchars($value);
				break;
				case 0:
					$value=$value;
				break;
			}
			
			$str.="<tr><th>".htmlspecialchars($fieldname)."</th><td>$value</td></tr>";
		}
		

		$str.="</table>";
		return $str;
	}	
}
