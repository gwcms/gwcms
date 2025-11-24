<?php

class GW_Bot_Detect
{
	
	static function isBot()
	{
		$ua  = $_SERVER['HTTP_USER_AGENT'] ?? false;
		
		return stripos($ua, 'bot')!==false  ||
				stripos($ua, 'spider')!==false || 
				stripos($ua, 'scrap')!==false ||
				stripos($ua, 'crawler')!==false
			;
	}	
	
	static function botRedirect()
	{		
		
		if (self::isBot() && date('w') == trim(file_get_contents('/var/www/common/no_botwday'))) {
			header("HTTP/1.1 503 Service Unavailable");
			header('Status: 503 Service Temporarily Unavailable');			
			header("Retry-After: 3600");
			exit("Site is temporarily unavailable for indexing. Please try again later.");
		}		
		
		
		//infinite loop stop
		if(self::isBot() && isset($_GET['after_auth_nav']))
		{
			header("HTTP/1.1 404 Not Found");
			exit("Site is temporarily unavailable for indexing. Please try again later.");
		}
		
				
	}
	
	static function process(){

		if( (GW::s('BOT_SEND_TO_MIRROR') && GW::s('PROJECT_ENVIRONMENT') == GW_ENV_TEST) || GW::s('SHADOW_SYS') ){
			//Mirror-Redirect-Domain
			if(isset($_SERVER['HTTP_MIRROR_REDIRECT_DOMAIN']))
				$_SERVER['HTTP_HOST']=$_SERVER['HTTP_MIRROR_REDIRECT_DOMAIN'];
			
			if(isset($_SERVER['HTTP_MIRROR_REDIRECT_CLIENT_IP']))
				$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_MIRROR_REDIRECT_CLIENT_IP'];
		}
		
		if(!GW::s('BOT_SEND_TO_MIRROR'))
			return false;
			
		$ua  = $_SERVER['HTTP_USER_AGENT'] ?? false;
		if(
			GW::s('BOT_SEND_TO_MIRROR') && self::isBot() && 
			(
				GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV || 
				GW::s('PROJECT_ENVIRONMENT') == GW_ENV_PROD
			)
		){
			
			
			//nebeatlaiko smurto 2025-04-04 2025-04-25 naujas serveriukas
			//
			self::botRedirect();		
			
			if(rand(0,10)==5)
				GW::db()->query("DELETE FROM `gw_mirror_serv_track` WHERE time < '" . date('Y-m-d H:i:s', strtotime('-10 minute')) . "'");
			
			//die('Temporarily off, update is in progress. Please come back later');
			$load1srv5min=self::getProcSpeed(5);
			if($load1srv5min > 1 ){
				$lgr = new GW_Logger(GW::s('DIR/LOGS').'slow_mirror_track.log');
				$lgr->msg($load1srv5min);
				
				header('HTTP/1.1 503 Service Temporarily Unavailable');
				header('Status: 503 Service Temporarily Unavailable');
				header('Retry-After: 300');//300 seconds
				exit;
			}

			initEnviroment(GW_ENV_TEST);
			$t = new GW_Timer;
			GW_Proxy_Site::redirect(GW::s('SITE_URL'));
			
			
			
			GW::db()->insert("gw_mirror_serv_track", ['servid'=>1,'time'=>date('Y-m-d H:i:s'), 'took'=>$t->stop()*100]);
			exit;
		}
		
		
	}

	static function getProcSpeed($min=1){
		return GW::db()->fetch_result("SELECT round(avg(took)/100, 2) FROM `gw_mirror_serv_track` WHERE `time` > '".
			date('Y-m-d H:i:s', strtotime("-$min minute")).
			"'");
	}
	
	static function increase2($table, $where, $field, $x = 1, $field2, $y=1, $nodie = false)
	{
		$query = "UPDATE $table SET `$field` = `$field` + $x, `$field2` = `$field2` + $y WHERE $where";

		GW::db()->query($query, $nodie);

		return GW::db()->affected();
	}
	

	static function ip2int($ip=false)
	{
		$ip = $ip ?: $_SERVER['REMOTE_ADDR'];
		return [$ip, sprintf('%u', ip2long($ip))];
	}
	
	static function ipStats() 
	{
		//tik tiems kurie neapsimeta botais gal dar i skaiciavimus dadet lenteles isbot
		if(self::isBot())
			return false;
		
		$cc = self::getCountryByIP();
		
		
		$adminid = $_SESSION['cms_auth']['user_id'] ?? false;
		$siteid = $_SESSION['site_auth']['user_id'] ?? false;
		
		list($ip, $ipint)  = self::ip2int();
		
		if($ip=='127.0.0.1')
			return false;
		

		$y = (int) date('Y');
		$m = (int) date('n');
		$d = (int) date('j');
		$h = (int) date('G');

		// 1️⃣ Check verification state first (cached by MySQL index)
		$state = GW::db()->fetch_result("SELECT state FROM request_ip_verify WHERE ip=$ipint LIMIT 1");
		//0 = normal, 1 = must verify, 2 = verified 3 whitelist


		// 2️⃣ Count requests per hour (atomic)
		$ua = self::getUserAgentId();
		$sql = "
			INSERT INTO request_ip_stats (year, month, day, hour, ip, cnt, ua)
			VALUES ($y, $m, $d, $h, $ipint, LAST_INSERT_ID(1), $ua)
			ON DUPLICATE KEY UPDATE cnt = LAST_INSERT_ID(cnt + 1);
		 ";
		
		GW::db()->query($sql);
		$count = GW::db()->fetch_result("SELECT LAST_INSERT_ID()");
		
		
		
		$maxcount = $cc == "LT" ? 1000 : GW::s('BOT_DETECT_IP_QUOTA_FOREIGN');
		
		if($state<1)
			GW::s('BOTDET_UNVERIFIED_COUNT_REMAIN', $maxcount-$count);
		
		//if($_SERVER['REMOTE_ADDR']=='88.223.24.240'){
		//	d::ldump(GW::s('BOTDET_UNVERIFIED_COUNT_REMAIN'));
		//}
		

		// 3️⃣ If too many requests — mark as must verify
		if ((!$adminid && !$siteid) && ($count > $maxcount || $state==1)) {
			
			if($state<1){
				//
				//file_put_contents(GW::s('DIR/TEMP').'testbot_verification', $count.'|'.$state.'|'.$ipint.'|'.$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);
				self::markIp(['state'=>1]);
			}
			
			if($state<2){
				
				
				//sleep(5); //slow down bots // negerai
				self::redirectIfNotVerified();
			}
		}

		// 4️⃣ Occasional cleanup (rare, safe)
		if (rand(0, 1000) == 1) {
			// cleanup old stats
			GW::db()->query("
			    DELETE FROM request_ip_stats
			    WHERE TIMESTAMP(CONCAT(year, '-', LPAD(month,2,'0'), '-', LPAD(day,2,'0'), ' ', LPAD(hour,2,'0'), ':00:00'))
				  < DATE_SUB(NOW(), INTERVAL 3 HOUR)
			");

			// cleanup expired verifications
			GW::db()->query("DELETE FROM request_ip_verify WHERE expires IS NOT NULL AND expires < NOW()");
		}
	}
	
	static function markIp($opts=[]){
		
		//return false;
		
		list($ip, $ipint)  = self::ip2int($opts['ip']??false);
		
		//xpires might be set in mysql but avoid mysql time zone diff
		//gwRawSql("DATE_ADD(NOW(), INTERVAL 10 DAY)"), // raw SQL 
		
		$vals = [
		    'ip'      => $ipint,
		    'state'   => $opts['state'] ?? 0,
		    'expires' =>  $opts['expires'] ?? date('Y-m-d H:i:s', strtotime("+10 DAYS")), 
		    'country' => self::getCountryByIP($opts['ip']??false), //del keshavimo false paduot kad greiciau suveiktu jei einajam variantui
		    'host'    => gethostbyaddr($ip),
		];
		
		//jei paduodamas jau ip kad neuzpildyit neto user agent
		if(!isset($opts['ip'])){
			$vals['ua'] = self::getUserAgentId();
		}
		
		if($opts['ua']){
			$vals['ua'] = $opts['ua'];
		}
		
		if($opts['tag'] ?? false){
			$vals['tag'] = $opts['tag'];
		}
		
		GW::db()->save('request_ip_verify', $vals);
		
		return $vals;
	}
	

	static function markIpAsVerified(string $ip, int $validForHours = 60) {
		$ipint = sprintf('%u', ip2long($ip));

		// Update state to verified (2) and set expiration
		$expires = date('Y-m-d H:i:s', strtotime("+$validForHours hours"));
		self::markIp(['state'=>2, 'expires'=>$expires]);
	}

	static function normalize_ua(string $ua): array {
		$orig = $ua;
		$u = strtolower($ua);

		// quick empty
		if (trim($u) === '') {
			return [
			    'family' => 'unknown', 'family_ver' => '0',
			    'os' => 'unknown', 'os_ver' => '0',
			    'device' => 'other', 'inapp' => null, 'is_bot' => false
			];
		}

		// ---- BOT / CRAWLER detection (list is non-exhaustive but broad) ----
		$bot_signatures = [
		    'googlebot', 'bingbot', 'yandexbot', 'baiduspider', 'duckduckbot',
		    'facebookexternalhit', 'facebookcatalog', 'twitterbot', 'slurp',
		    'applebot', 'linkedinbot', 'ahrefsbot', 'semrushbot', 'mj12bot',
		    'pingdom', 'uptimerobot', 'monitoring', 'crawler', 'spider', 'bot', 'robot'
		];
		foreach ($bot_signatures as $b) {
			if (strpos($u, $b) !== false) {
				// map some bots to friendly names
				$name = $b;
				if (strpos($u, 'facebookexternalhit') !== false)
					$name = 'facebookexternalhit';
				if (strpos($u, 'googlebot') !== false)
					$name = 'googlebot';
				return [
				    'family' => 'bot_' . $name,
				    'family_ver' => '0',
				    'os' => 'server',
				    'os_ver' => '0',
				    'device' => 'bot',
				    'inapp' => null,
				    'is_bot' => true
				];
			}
		}

		// ---- in-app / wrappers detection ----
		$inapp = null;
		if (strpos($u, 'fbav/') !== false || strpos($u, 'fban/') !== false || strpos($u, 'fb_iab') !== false || strpos($u, 'fb_') !== false)
			$inapp = 'facebook';
		if (strpos($u, 'gsa/') !== false || strpos($u, 'googleapp') !== false)
			$inapp = 'gsa';
		if (strpos($u, 'line/') !== false)
			$inapp = 'line';
		if (strpos($u, 'naver') !== false)
			$inapp = 'naver';
		if (strpos($u, 'microMessenger') !== false || strpos($u, 'wechat') !== false)
			$inapp = 'wechat';
		if (strpos($u, 'yahoo') !== false)
			$inapp = 'yahoo';
		if (strpos($u, 'instagram') !== false)
			$inapp = 'instagram';
		if (strpos($u, 'messenger') !== false)
			$inapp = 'messenger';

		// ---- DEVICE detection (mobile/tablet/desktop) ----
		$device = 'desktop';
		if (preg_match('/mobile|iphone|ipod|android .*mobile|crios\/|fennec|phone|opera mini|bb10/i', $u))
			$device = 'mobile';
		if (preg_match('/ipad|tablet|nexus 7|nexus 9|kindle|silk|playbook/i', $u))
			$device = 'tablet';
		if (strpos($u, 'tv') !== false || strpos($u, 'smarttv') !== false || strpos($u, 'googletv') !== false || strpos($u, 'appletv') !== false)
			$device = 'tv';

		// ---- OS detection ----
		$os = 'unknown';
		$os_ver = '0';
		if (preg_match('/windows nt\s*([0-9\.]+)/i', $u, $m)) {
			$os = 'windows';
			// map nt numbers to friendly major (optional coarse)
			$nt = $m[1];
			// keep only major or common friendly map
			$map = [
			    '10.0' => '10', '6.3' => '8.1', '6.2' => '8', '6.1' => '7', '6.0' => 'vista',
			];
			$os_ver = $map[$nt] ?? explode('.', $nt)[0];
		} elseif (preg_match('/android\s*([0-9\.]+)/i', $u, $m)) {
			$os = 'android';
			$os_ver = explode('.', $m[1])[0];
		} elseif (preg_match('/cpu (?:iphone )?os\s*([0-9_]+)/i', $u, $m)) {
			$os = 'ios';
			$os_ver = str_replace('_', '.', explode('_', $m[1])[0]);
		} elseif (preg_match('/iphone|ipad|ipod/i', $u)) {
			$os = 'ios';
			$os_ver = '0';
		} elseif (preg_match('/mac os x\s*([0-9_]+)/i', $u, $m)) {
			$os = 'macos';
			$os_ver = str_replace('_', '.', explode('_', $m[1])[0]);
		} elseif (preg_match('/harmonyos|huawei/i', $u)) {
			$os = 'harmonyos';
			$os_ver = '0';
		} elseif (strpos($u, 'linux') !== false) {
			$os = 'linux';
			$os_ver = '0';
		} elseif (strpos($u, 'cros') !== false || strpos($u, 'chrome os') !== false) {
			$os = 'chromeos';
			$os_ver = '0';
		} elseif (strpos($u, 'windows phone') !== false) {
			$os = 'windowsphone';
			$os_ver = '0';
		}

		// ---- Browser family detection & major version extraction ----
		// Order matters: detect vendor wrappers (SamsungBrowser, CriOS) before generic 'chrome'
		$family = 'other';
		$family_ver = '0';

		// helper: extract major from version string like "133.0.6943.120" -> "133"
		$get_major = function ($v) {
			if (!$v)
				return '0';
			$v = trim($v);
			$v = preg_replace('/[^0-9\.]/', '', $v);
			$parts = explode('.', $v);
			return $parts[0] ?? '0';
		};

		if (preg_match('/samsungbrowser\/([0-9\.]+)/i', $u, $m)) {
			$family = 'samsung';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/edg[a|b|ios]*\/([0-9\.]+)/i', $u, $m)) {
			$family = 'edge';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/opr\/([0-9\.]+)|opera\/([0-9\.]+)/i', $u, $m)) {
			$v = $m[1] ?: $m[2] ?? '';
			$family = 'opera';
			$family_ver = $get_major($v);
		} elseif (preg_match('/crios\/([0-9\.]+)/i', $u, $m)) {
			$family = 'chrome_ios';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/chrome\/([0-9\.]+)/i', $u, $m) && strpos($u, 'safari') !== false) {
			// treat as chrome if chrome/ present (many webviews)
			$family = 'chrome';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/firefox\/([0-9\.]+)/i', $u, $m)) {
			$family = 'firefox';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/version\/([0-9\.]+).*safari/i', $u, $m) || preg_match('/safari\/([0-9\.]+)$/i', $u, $m)) {
			// Version/xx Safari/xxx -> use Version for major
			$ver = $m[1] ?? '';
			if (preg_match('/version\/([0-9\.]+)/i', $u, $m2))
				$ver = $m2[1];
			$family = 'safari';
			$family_ver = $get_major($ver);
		} elseif (preg_match('/ucbrowser\/([0-9\.]+)/i', $u, $m)) {
			$family = 'uc';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/mozilla\/[0-9\.]+ .*rv:([0-9\.]+).*gecko/i', $u, $m)) {
			// fallback for old Firefox-like
			$family = 'firefox';
			$family_ver = $get_major($m[1]);
		} elseif (preg_match('/applewebkit\/[0-9\.]+.*chrome/i', $u) && preg_match('/safari/i', $u)) {
			// fallback to chrome
			if (preg_match('/chrome\/([0-9\.]+)/i', $u, $m)) {
				$family = 'chrome';
				$family_ver = $get_major($m[1]);
			}
		} else {
			// extra tries: MSIE, Trident
			if (preg_match('/msie\s*([0-9\.]+)/i', $u, $m) || preg_match('/trident\/.*rv:([0-9\.]+)/i', $u, $m)) {
				$family = 'ie';
				$family_ver = $get_major($m[1]);
			} else {
				// last resort detect token-based families
				if (strpos($u, 'micromessenger') !== false) {
					$family = 'wechat';
					$family_ver = '0';
				} elseif (strpos($u, 'line/') !== false) {
					$family = 'line';
					$family_ver = '0';
				} elseif (strpos($u, 'telegram') !== false) {
					$family = 'telegram';
					$family_ver = '0';
				} else {
					$family = 'other';
					$family_ver = '0';
				}
			}
		}

		// collapse many minor Chrome-like versions into coarse buckets (optional)
		// e.g., keep only major number (already done) — good for reducing cardinality.

		return [
		    'family' => $family,
		    'family_ver' => (string) $family_ver,
		    'os' => $os,
		    'os_ver' => (string) $os_ver,
		    'device' => $device,
		    'inapp' => $inapp,
		    'is_bot' => false
		];
	}

	static function compact_key(array $info): string {
		// Normalization: lowercase, remove spaces
		$parts = [];
		$fam = $info['family'] ?? 'other';
		$fv = $info['family_ver'] ?? '0';
		$os = $info['os'] ?? 'unknown';
		$ov = $info['os_ver'] ?? '0';
		$device = $info['device'] ?? 'other';
		$inapp = $info['inapp'] ?? '';

		// collapse unknown / zero versions to just family/os
		$famPart = ($fv && $fv !== '0') ? "{$fam}_{$fv}" : $fam;
		$osPart = ($ov && $ov !== '0') ? "{$os}_{$ov}" : $os;

		$parts[] = $famPart;
		$parts[] = $osPart;
		$parts[] = $device;
		if ($inapp)
			$parts[] = $inapp;

		
		if($famPart =='other' && $osPart=='unknown'){
			$logfile =  . GW::s('DIR/LOGS').'user_agent_fail_identify.log';
			$ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
			$output = file_put_contents($logfile, $ua."\n", FILE_APPEND);
		}
		
		// join with pipe to keep keys readable and unique
		return implode('|', $parts);
	}

	static function getUserAgentId()
	{
		static $uaid;
		
		if($uaid)
			return $uaid;
		

	
		$user_agent = self::compact_key(self::normalize_ua($_SERVER['HTTP_USER_AGENT'] ?? '-'));



		$uaid = GW_Uni_Schema::getIdxByStr('ua', $user_agent);
		
		return $uaid;
	}
	
	static function getCountryByIP($ip=false)
	{
		
		if($ip)
			return geoip_country_code_by_name($ip);
		
		//keshuotas variantas veiktu tik jei ziurima einamajam klientui
		static $cc;
		
		if($cc)
			return $cc;
		
		$cc = geoip_country_code_by_name($_SERVER['REMOTE_ADDR']);
		
		return $cc;
	}
	
	static function stats(){
		
		$date= date('Y-m-d');
		$speed = GW::$globals['proc_timer']->stop(1);
		
		if(isset($_GET['bottest']))
			d::dumpas([$user_agent, $speed]);
		
		$user_agent_id = self::getUserAgentId();
		
		$aff = self::increase2("request_by_user_agent", GW_DB::prepare_query(['date=? AND user_agent=?',$date,$user_agent_id]),'cnt',1,'speed', $speed);;
		
		
		if(!$aff)
			GW::db()->insert("request_by_user_agent", ['date'=>$date,'user_agent'=>$user_agent_id, 'cnt'=>1]);
		
		if($speed>3){
			
			GW::db()->insert("request_slow", ['url'=>$_SERVER['REQUEST_URI'],'ip'=>$_SERVER['REMOTE_ADDR'],'user_agent'=>$user_agent_id, 'speed'=>$speed]);
		}
	}
	
	
	static function initSession()
	{
		if(isset($_GET['GWSESSID'])){
			session_id($_GET['GWSESSID']);
		}else{
			session_start();
		}		
	}
	
	static function recaptcha()
	{
		self::initSession();
		self::ipStats();


		$special_domains = GW::s('SOLVE_RECAPTCHA_DOMAINS'); // domains that need captcha
		$current_domain = $_SERVER['HTTP_HOST'] ?? '';

		//
		if ( $special_domains && GW::s('SOLVE_RECAPTCHA_PUBLIC_PRIVATE') && in_array($current_domain, $special_domains)) {
		    // If not yet verified, redirect to captcha
			
		    self::redirectIfNotVerified();
		}		
	}
	
	static function redirectIfNotVerified(){
		
		if(!GW::s('SOLVE_RECAPTCHA_PUBLIC_PRIVATE'))
			return false;
		
		if (empty($_SESSION['human_verified'])) {
			$_SESSION['redirect_after_captcha'] = $_SERVER['REQUEST_URI'];
			header('Location: /humancheck.php');
			exit;
		    }		
	}
	
	
}