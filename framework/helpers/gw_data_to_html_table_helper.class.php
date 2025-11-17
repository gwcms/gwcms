<?php

class GW_Data_to_Html_Table_Helper
{

	
	static function doTablePlain($data, array $opts=[])
	{
		if (!is_array($data))
			return [];
		
	
		$keys = [];
		foreach($data as $i => $row)
			foreach($row as $key => $whatever)
				$keys[$key] = 1;
		
		$keys = array_keys($keys);
		
		$table = [];
		$table[] = array_values($keys);
		
		$merge = [];
		

		
		foreach ($data as $i => $row) {
			$trow = [];
			foreach ($keys  as $field){
				$val = $row[$field] ?? false;
				$trow[] = $val;
			}
			
			$table[] = $trow;
		}

		return $table;
	}
	
	
	static function doTable($data, array $opts=[])
	{
		$escape = $opts['escape'] ?? 1;
		$mergegroup = $opts['mergegroup'] ?? 0;
		
		if (!is_array($data))
			return;

		$keys = [];
		foreach($data as $i => $row)
			foreach($row as $key => $whatever)
				$keys[$key] = 1;
		
		$keys = array_keys($keys);
		
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
			foreach ($keys  as $field){
				$val = $row[$field] ?? false;
				
				self::valformat($field, $val, $opts);				
				
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
			self::valformat($fieldname, $value, $opts);
			
			$str.="<tr><th>".htmlspecialchars($fieldname)."</th><td>$value</td></tr>";
		}
		

		$str.="</table>";
		return $str;
	}

	static function valformat($fieldname, &$value, $opts=[])
	{
		if(is_array($value) || is_object($value)){
			
			switch($opts['arrayformat'][$fieldname] ?? 2){
				case 1:
					$value = is_array($value) ? implode(", \n", $value) :  $value;
				break;
				case 2:
					$value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				break;			
			}
			
		}
		
			

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
			case 'trunc40':
				$value='<span title="'.htmlspecialchars($value).'">'.htmlspecialchars(GW_String_Helper::truncate($value, 40))."</span>";
			break;
		}
	}
}
