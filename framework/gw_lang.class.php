<?php

class GW_Lang {

	static $cache;
	static $ln;
	static $langf_dir;
	static $module_dir;
	static $module;

	static function getGlobalLangFile($file_id) {
		return (self::$langf_dir ? self::$langf_dir : GW::s('DIR/ADMIN/LANG')) . "{$file_id}.lang.xml";
	}

	static function getModuleLangFile($module) {
		return self::$module_dir . $module . "/lang.xml";
	}

	static function loadFile($file_id, $modulename = '') {
		//$file_id=strtolower($file_id);
		$cid = $file_id . '/' . $modulename;

		if (isset(self::$cache[$cid]))
			return true;

		if ($modulename) {
			$filename = self::getModuleLangFile($modulename);
		} else {
			$filename = self::getGlobalLangFile($file_id);
		}




		if (!is_file($filename)) {
			d::dump('lang file "' . $filename . '" do not exist', E_USER_NOTICE);
			return false;
		}

		self::$cache[$cid] = GW_Lang_XML::load($filename, self::$ln);

		return true;
	}

	static function &getFromCache($file_id, $module, $path, $create = false) {
		$false = false;
		$var = & self::$cache[$file_id . '/' . $module];

		//grazinti visa faila
		if ($path == [''])
			return $var;

		foreach ($path as $key) {
			if (!$create)
				if (!is_array($var) || !isset($var[$key]))
					return $false;

			$var = & $var[$key];
		}


		return $var;
	}

	static function &readG($file_id, $module, $path, $create = false) {
		$null = null;

		$file_id = strtolower($file_id);
		$module = strtolower($module);

		if (!self::loadFile($file_id, $module))
			return $key;


		$tmp = & self::getFromCache($file_id, $module, explode('/', $path), $create);


		if ($tmp || $create)
			return $tmp;
		else
			return $null;
	}

	/**
	 * Code example: /ERROR_FILE/PATH/TO/TEXT
	 * Note: code should start with "/"
	 * 
	 * file stored in:
	 * ADMIN_DIR/lang/strtolower(ERROR_FILE)_error.lang.Error_Message::$ln.xml
	 */
	// /g/globalus_vertimo failas
	// /G/globalausfailopavadinimas/kelias 
	// /m/vertimo/kelias - sio modulio vertimai
	// /M/modulis/kelias
	// /A/alternatyvus vertimas - jei ras modulyje ims is modulio, jei neras ieskos application.lang.xml
	// key butinai turi prasidet ne "/" - tuo atveju bus rodomas pats key	
	static function &readWrite($key, $write = null) {
		if ($key[0] != '/')
			return $key;

		list(, $type, $otherargs) = explode('/', $key, 3);


		$create = $write !== null;

		switch ($type) {
			case 'G': // /G/failopavadinimas/kelias 
				list($fileid, $path) = explode('/', $otherargs, 2);

				$r = & self::readG($fileid, '', $path, $create);
				break;
			case 'g'://globalus_vertimo failas
				$r = & self::readG('application', '', $otherargs, $create);
				break;
			case 'M':
				list($module, $path) = explode('/', $otherargs, 2);
				$r = & self::readG('', $module, $path, $create);
				break;
			case 'm':
				$r = & self::readG('', self::$module, $otherargs, $create);
				break;

			case 'A':
				$r = & self::readG('', self::$module, $otherargs);
				if (!$r)
					$r = & self::readG('application', '', $otherargs, $create);
				break;
		}


		if ($write !== null) {
			$r = $write;
		}

		if ($r !== null || $write)
			return $r;
		else
			return $key;
	}

}
