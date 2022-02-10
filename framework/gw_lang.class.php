<?php

class GW_Lang
{

	static $cache;
	static $ln;
	static $langf_dir;
	static $module_dir;
	static $module;
	static $debug=false;
	static $app='ADMIN';
	
	static function setCurrentLang($ln)
	{
		self::$ln = $ln;
	}
	
	static function setCurrentApp($app)
	{
		self::$app = strtoupper($app);
	}
	
	static function setDebug($debug)
	{
		self::$debug = $debug;
	}
	
	static function getGlobalLangFile($file_id)
	{		
		return GW::s("DIR/".self::$app."/LANG") . $file_id . ".lang.xml";
	}

	static function getModuleLangFile($module)
	{		
		return GW::s("DIR/".self::$app."/MODULES") . $module . "/lang.xml";
	}

	static function loadFile($file_id, $modulename = '')
	{
		//$file_id=strtolower($file_id);
		$cid = self::$app.'/'.self::$ln.'/'.$file_id . '/' . $modulename;

		if (isset(self::$cache[$cid]))
			return true;

		if ($modulename) {
			$filename = self::getModuleLangFile($modulename);
		} else {
			$filename = self::getGlobalLangFile($file_id);
		}




		if (!is_file($filename)) {
			//d::dump('lang file "' . $filename . '" do not exist', E_USER_NOTICE);
			return false;
		}

		self::$cache[$cid] = GW_Lang_XML::load($filename, self::$ln);

		return true;
	}
	
	static function getFilename($trans)
	{
		$filename = false;
		
		list($root, $name) = explode('/', $trans);
		
		switch($root){
			case 'G': // /G/failopavadinimas/kelias 
				$filename = self::getGlobalLangFile($name);
			break;
			case 'M':
				$filename = self::getModuleLangFile($name);		
			break;
		}
		
		
		return $filename;
		
	}

	static function &getFromCache($file_id, $module, $path, $create = false)
	{
		$false = false;
		
		$cid = self::$app.'/'. self::$ln.'/'.$file_id . '/' . $module;
		$var = & self::$cache[$cid];

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

	static function &readG($file_id, $module, $path, $create = false)
	{
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
	// opts - asis: (AsIs - return null if not exists) 	
	static function readWrite($key, $opts=[])
	{
		if ($key[0] != '/')
			return $key;

		list(, $type, $otherargs) = explode('/', $key, 3);
		
		if($type=="APP")
		{
			$prevapp = GW_Lang::$app;
			
			list($app, $key) = explode('/', $otherargs, 2);
			$key = '/'.$key;
			
			GW_Lang::setCurrentApp($app);
			
			$res = self::readWrite($key, $opts);	
			
			GW_Lang::setCurrentApp($prevapp);
			
			return $res;
		}
		
		if($type=="LN")
		{
			$prevln = GW_Lang::$ln;
			
			list($ln, $key) = explode('/', $otherargs, 2);
			$key = '/'.$key;
			
			GW_Lang::setCurrentLang($ln);
			
			$res = self::readWrite($key, $opts);
						
			GW_Lang::setCurrentLang($prevln);
			
			return $res;
		}

		self::$last_realkey = '';

		switch ($type) {
			case 'G': // /G/failopavadinimas/kelias 
				list($fileid, $path) = explode('/', $otherargs, 2);

				$r = & self::readG($fileid, '', $path);
				break;
			case 'g'://globalus_vertimo failas
				$r = & self::readG('application', '', $otherargs);
				self::$last_realkey = 'G/application/'. $otherargs;
				break;
			case 'M':
				list($module, $path) = explode('/', $otherargs, 2);
				$r = & self::readG('', $module, $path);
				break;
			case 'm':
				$r = & self::readG('', self::$module, $otherargs);
				self::$last_realkey = 'M/'.self::$module.'/'. $otherargs;
				break;

			case 'A':
				$r = & self::readG('', self::$module, $otherargs);
				self::$last_realkey = 'M/'.self::$module.'/'. $otherargs;;
				
				if (!$r){
					
					$r = & self::readG('application', '', $otherargs);
					
					if($r)
						self::$last_realkey = 'G/application/'. $otherargs;
					
				}
				break;
		}

		
		
		if(isset($prevapp))
			GW_Lang::setCurrentApp($prevapp);


		if ($r === null ){
			if($opts['asis']??false){
				return null;
			}else{
				return $key;
			}
		}else{
			return $r;
		}
	}
	
	static $last_realkey='';
	
	
	
	static function transKeyAnalise($fullkey)
	{
		list(, $module, $key) = explode('/', $fullkey, 3);
		
		if ($module == 'M') {
			list($module, $key) = explode('/', $key, 2);
			$module = 'M/' . strtolower($module);
		} elseif ($module == 'm') {
			$module = 'M/' . GW_Lang::$module;
		} elseif ($module == 'g') {
			$module = 'G/application';
		} elseif ($module == 'G') {
			list($module, $key) = explode('/', $key, 2);
			$module = 'G/' . strtolower($module);
		}

		return [$module, $key];
	}
	
	
	static $transcache;
	
	
	
	
	static function __dbTrans2arr($tr, &$arr){
		foreach ($tr as $k => $val) {

			$var =& $arr;

			foreach (explode('/', $k) as $kk){
				if(is_string($var))
					break;

				$var = &$var[$kk];
			}

			$var = $val;
		}
	}
	
	/**
	 * užkrauna vertimus iš db i cache, konvertuoja į masyvą
	 */
	static function &transCache($module)
	{
		$cid = GW_Lang::$ln.'/'.$module;
		
		if (!isset(self::$transcache[$cid])) {
			$tr = GW_Translation::singleton()->getAssoc(['key', 'value_' . GW_Lang::$ln], ['module=?', $module], ['order' => 'priority ASC']);
			
			self::__dbTrans2arr($tr, self::$transcache[$cid]);
		}
		
		return self::$transcache[$cid];
	}
	
	static function transCacheReset($module)
	{
		$cid = GW_Lang::$ln.'/'.$module;
		unset(self::$transcache[$cid]);
	}
	
	static function __highlightActive()
	{
		return (GW::$context->app->user && 
			GW::$context->app->user->is_admin || GW::$devel_debug ) && 
			isset(GW::$context->app->sess['lang-results-active']) && 
			GW::$context->app->sess['lang-results-active'] && !isset($_GET['lang-edit-of']);		
	}
	
	static $developLnResList = [];
	
	static function lnResult($key, &$result, $orig_val=false, $afteronly=false)
	{
		if(!self::__highlightActive()) 
			return $result;
	
		
		$ret_rich = is_array($result) ? $result : "<span class='lnresult' data-module='".self::$module."' data-key='".$key."' data-val='". htmlspecialchars($orig_val ? $orig_val : $result)."'>".$result."</span>";
		
		//allow only root user to edit abstract translations within module context
		if(self::$module && strpos($key, 'G/application')===0 && GW::$context->app->user && GW::$context->app->user==9){
			$ret_rich = $result;
		}
		
		if($afteronly){
			$ret = $result;
		}else{
			$ret = $ret_rich;
		}
		
		if(!is_array($result))
			self::$developLnResList[$key] = $ret_rich;
		
		
		return $ret;
	}
	
	
	/**
	 * pakrauna vertimus is duombazes, 
	 * jei nera duombazeje tada pakrauna is 
	 * lang failu arba pacio templeito jei vartotojas developeris
	 * opcijos
	 *	nocreate - nekurti vertimo
	 *	v - vertimu kintamieji
	 *	c - 1 lowercase, 2 - pirma didzioji
	 *	l - linksniavimas
	 *	asis - jei neegzistuoja grazins null
	 *	
	 */
	static function ln($fullkey, $opts=[])
	{		
		if($fullkey[0]!=='/')
			return $fullkey;
		
		list($module, $key) = GW_Lang::transKeyAnalise($fullkey);
		
		if($module == "LN")
		{
			list($ln, $fullkey) = explode('/', $key, 2);
			
			$prevln = GW_Lang::$ln;
			GW_Lang::$ln = $ln;
			$result = GW_Lang::ln('/'.$fullkey, $opts);
			//$result = GW_Lang::ln('/'.$fullkey, $valueifnotfound);
			
			GW_Lang::$ln = $prevln;
			
			return $result;
		}
		
		$orig_key = $module.'/'.$key;
		
		
		if($tmp=GW_Lang::transOver($orig_key))
			return $tmp;		

		//uzloadinti vertima jei nera uzloadintas
		
		$transcache = GW_Lang::transCache($module);
				
		//paimti vertima is cache
		
		$vr = GW_Array_Helper::getPointer2XlevelAssocArrNew($transcache, explode('/', $key));
		
		//d::dumpas([$transcache, $vr, explode('/', $key)]);
		if($opts['asis'] ?? false )
			return $vr;
		
		
		

		//nerasta verte arba verte su ** reiskias neisversta - pabandyti automatiskai importuoti
		if (GW::$devel_debug && !isset($opts['nocreate']) && ($vr == Null || (is_string($vr) && $vr[0] == '*' && $vr[strlen($vr) - 1] == '*'))) {
			
			if(!isset($opts['redirect'])){
				$keyx=self::transKeyAnalise($fullkey);
				
				$resraw=file_get_contents('https://voro.lt/service/trshare/gettr?trkey='.implode('/',$keyx));


				if($resp = json_decode($resraw, true)){


					if($resp['result']){
						$newtr = GW_Translation::singleton()->createNewObject($resp['result']);
						$newtr->trshare = 1;
						$newtr->replaceInsert();
						$opts['redirect'] = 1;
						self::transCacheReset($keyx[0]);;
						
						return GW::ln($fullkey, $opts);
					}	
				}else{
					//trans share mechanism failed
				}
			}
			
			//jei tokia pat kalba ir verte nerasta ikelti vertima i db
			//if ($valueifnotfound && strpos($valueifnotfound, GW_Lang::$ln . ':') !== false) {
			//	list($ln, $vr) = explode(':', $valueifnotfound, 2);
			///	GW_Translation::singleton()->store($module, $key, $vr, GW_Lang::$ln);
			//} else {
				//is lang failu
				$fromxml = GW::l($fullkey);
				$vr = $fromxml != $fullkey ? $fromxml : '*' . $key . '*';

				
				//5argumentas: self::$app == 'ADMIN' ? 1 : 0
				GW_Translation::singleton()->store($module, $key, $key, GW_Lang::$ln);
			//}
			return self::lnResult($orig_key, $orig_key, $orig_val ?? false);
		}
		
		if(!$vr)
			return $vr=$orig_key;
		
		$orig_val = false;
			
		self::optProc($vr, $opts, $orig_val);		
		
		return self::lnResult($orig_key, $vr, $orig_val);
	}
	
	static function optProc(&$vr, &$opts, &$orig_val=false)
	{
		if(isset($opts['l']) && self::$ln=='lt'){
			if($orig_val ==false)
				$orig_val = $vr;
			
			$vr = GW_Linksniai_Helper::getName ($vr, $opts['l']);
		}
		
		if(isset($opts['c'])){
			if($orig_val ==false)
				$orig_val = $vr;
			
			if($opts['c']==1){
				$vr = mb_strtolower($vr);
			}elseif($opts['c']==2){
				$vr = GW_String_Helper::ucfirst($vr);
			}elseif($opts['c']==3){
				$vr = mb_strtoupper($vr);
			}
		}
		
		if(isset($opts['v'])){
			if($orig_val ==false)
				$orig_val = $vr;
			
			foreach($opts['v'] as $key => $val)
				$vr = str_replace ('$'.$key, $val, $vr);
		}		
	}
	
	static function l($fullkey, $opts=[])
	{
		

		
		list($module, $key) = GW_Lang::transKeyAnalise($fullkey);
		
		
		$orig_key = $module.'/'.$key;	
		
		
		
		$vr = self::readWrite($fullkey, $opts);
		
		if(self::$last_realkey)
			$orig_key = self::$last_realkey;

		
		$orig_val = false;
		self::optProc($vr, $opts, $orig_val);
		
		$afteronly = ($opts['asis'] ?? false) || ($opts['noedit'] ?? false);
		
		
		
		
		
		return self::lnResult($orig_key, $vr, $orig_val, $afteronly);
	}
	
	
	
	
	
	static $transOverContextGroup;
	static $transOverContextId;
	static $transOverCache;
	
	static function transOverSetContext($group, $id)
	{
		self::$transOverContextGroup = $group;
		self::$transOverContextId = $id;
	}
	
	static function &transOverLoadCache($cid)
	{		
		if (!isset(self::$transOverCache[$cid])) {
			$tr = GW_Translation_Over::singleton()->getAssoc(
				['concat(`module`,"/",`key`)', 'value_' . GW_Lang::$ln], 
				['context_group=? AND context_id=?', self::$transOverContextGroup, self::$transOverContextId], 
				['order' => 'priority ASC']
			);

			
			self::__dbTrans2arr($tr, self::$transOverCache[$cid]);
		}
		
		return self::$transOverCache[$cid];
	}
	
	static function transOverlnResult($key, $owner, &$result)
	{
		if(!self::__highlightActive()) 
			return $result;
		
		return is_array($result) ? $result : "<span class='lnresult transover' data-key='".$key."' data-owner='".$owner."'>".$result."</span>";
	}	
	
	
	static function transOver($key)
	{
		if(!(self::$transOverContextGroup && self::$transOverContextId))
			return false;
				
		$cache = self::transOverLoadCache($ownr=self::$transOverContextGroup.'/'.self::$transOverContextId);	
		//d::dumpas($cache);
		$xpld = explode('/', $key);
		
		$vr = GW_Array_Helper::getPointer2XlevelAssocArr($cache, $xpld);
		
		if(!$vr)	
			return false;
		
		return self::transOverlnResult($key, $ownr, $vr);
		
	}
	
	
}
