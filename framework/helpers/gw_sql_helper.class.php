<?php

class GW_SQL_Helper {

		static function condition_str($array, $implode = ' AND ') {
				$str = '';

				foreach ($array as $key => $val) {
						$str .= ($str ? $implode : '') . "`$key`= '" . GW_DB::escape($val) . "'";
				}

				return $str;
		}

}
