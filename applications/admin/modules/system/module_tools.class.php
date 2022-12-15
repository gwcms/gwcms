<?php


class Module_Tools extends GW_Common_Module
{	
	public $default_view = 'default';
		
	function init()
	{
		$this->model = new stdClass();
		
		
		parent::init();
	}

	
	function viewDefault()
	{
		list($vars['lastupdates'], $vars['updatefiles']) = $this->__doImportSqlUpdates_list2update();
		
		
		$test_actions = [];
		$test_views = [];
		
		$list = get_class_methods ($this);
		foreach($list as $method){

			if(stripos($method, 'doTest')===0)
				$test_actions[]=[$method, $this->$method];
			
			if(stripos($method, 'viewTest')===0)
				$test_views[]=[substr($method,4), $this->$method];			
		}
				
		$this->tpl_vars['test_actions']=$test_actions;
		$this->tpl_vars['test_views']=$test_views;
		
		
		$this->diskUsageData();
				

		return $vars;
	}
	
	function diskUsageData()
	{
		$rows = shell_exec("du -S -m --apparent-size ".GW::s('DIR/REPOSITORY'));
		$list = [];
		$total_size = 0;
		
		foreach(explode("\n", $rows) as $row){
			@list($size, $name) = explode("\t", $row);
			$name = str_replace(GW::s('DIR/REPOSITORY'), '', $name);
			$total_size += (int)$size;
			
			if($size)
				$list[] = ['name'=>($name ? $name : './repository').' ('.$size.' MB)', 'y'=>(int)$size];
		}
		$list_large_dirs = $list;	
		$others = 0;
		
		foreach($list_large_dirs as $idx => $row){
			if($row['y']/$total_size < 0.01){
				$others += $row['y'];
				unset($list_large_dirs[$idx]);
			}else{
				$list_large_dirs[$idx]['r'] = $row['y']/$total_size ;
			}
		}
		$list_large_dirs = array_values($list_large_dirs);
		$list_large_dirs[] = ['name'=>"Others ($others MB)", 'y'=>$others];
		//d::dumpas([$list_large_dirs, $total_size, $list]);
		
		$this->tpl_vars['diskusagedata_total'] = $total_size;
		$this->tpl_vars['diskusagedata']=$list_large_dirs;		
	}
	

	function doInstall()
	{
		$this->log[] = GW_Install_Helper::CheckFolders();
	}
	
	function doDebugModeToggle()
	{
		if($_GET['app'] ?? false=="SITE")
		{
			$var  =& $_SESSION['SITE']['debug'];
		}else{
			$var  =& $this->app->sess['debug'];
		}
		
		$var=(int)$var;
		$var=($var+1) % 2;
		
		
		if(isset($_GET['uri'])){
			header("Location: ".$_GET['uri']);
			exit;
		}
		
		$this->jump();
	}
	
	
	
	
	function __executeQuery($sql)
	{
		
		$sqls = explode(';', $sql);
		
		$db =& $this->app->db;
		
		foreach($sqls as $sql)
		{
			if(!trim($sql))continue;
					
			$db->query($sql, true);
			$aff = $db->affected();

			
			if($db->error)
				$this->app->setError($db->error .' Query: '.$db->error_query);
			
			$this->setPlainMessage("<pre>".htmlspecialchars($sql).";\n<b># Affected rows:</b> ".$aff."</pre>");
		}
	}	
	
	
	
	function __doImportSqlUpdates_list2update()
	{
		$lastupdates= GW::getInstance('GW_Config')->get('gwcms/last_sql_updates');
		
		
		$list_files = glob(GW::s('DIR/ROOT').'sql/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]-[0-9]*.sql');
		
		$updates = [];
		
		foreach($list_files as $filename)
		{
			if(basename($filename) > $lastupdates)
				$updates[] = $filename;
		}
		
		return [$lastupdates, $updates];
	}
	
	function doImportSqlUpdates()
	{
		list($lastupdates, $updates) = $this->__doImportSqlUpdates_list2update();
		
		foreach($updates as $updatefile)
		{
			$sqls = file_get_contents($updatefile);
			$this->__executeQuery($sqls);
			
			GW::getInstance('GW_Config')->set('gwcms/last_sql_updates', basename($updatefile));
		}
		
		$this->jump();
	}
	
	
	public $viewtestViewPHPinfo = ["info"=>"phpinfo()"];
	
	function viewtestViewPHPinfo()
	{
		//
		echo phpinfo();
		
	}
	function viewCompatability()
	{
		$info = PHP_Info_Parser_Helper::parse();
		
		$gd_info=gd_info();
		$apache_loaded_modules=apache_get_modules();
		
		exec('unzip',$unzip);
		
		//satisfies
		$comp = Array
		(
			'phpversion'=>Array('required'=>'5.2.10','current'=>phpversion()),
			'magic_quotes'=>Array('required'=>0, 'current'=>ini_get('magic_quotes_gpc')),
			'short_open_tag'=>Array('required'=>1, 'current'=>ini_get('short_open_tag')),
			'mysql'=>Array('required'=>'5.1','current'=>$this->app->db->fetch_result('SELECT VERSION();')),
			'apache'=>Array('required'=>'2','current'=>$_SERVER['SERVER_SOFTWARE']),
			'gd'=>Array('required'=>'2','current'=>$gd_info['GD Version']),
			'apache_modules'=>Array
			(
				'mod_rewrite'=>Array('required'=>'1', 'current'=>(int)in_array('mod_rewrite', $apache_loaded_modules)),
			),
			'safe_mode'=>Array('current'=>(int)ini_get('safe_mode')),
			'upload_max_filesize'=>Array('current'=>ini_get('upload_max_filesize')),
			'post_max_size'=>Array('current'=>ini_get('post_max_size')),
			'exec unzip'=>Array('current'=> $unzip[0]),
			'admin/cli/system.php process id'=>GW_App_System::getRunningPid()
		);
		
		$this->log[]=$comp;
	}
	
	
	public $viewTestListIcons = ["info"=>"Shows gwcms icons list"];
	
	function viewTestListIcons()
	{
		
		$data = file_get_contents(GW::s('DIR/ADMIN/ROOT').'static/fonts/gwcms/style.css');
		
		//d::dumpas($data);
		
		preg_match_all('/\.(gwico\-.*?):before/', $data, $matches);
		
		$this->tpl_vars['list'] = $matches[1];
		
		
	}
	
	public $viewTestFavIco = ["info"=>"FavIcon editor"];
	
	function viewTestFavIco()
	{
		
		$fontsdir=GW::s('DIR/APPLICATIONS').'admin/static/fonts/';		
		$fnts =  glob($fontsdir.'*.{ttf,otf}', GLOB_BRACE);
		$fnts = array_map('basename', $fnts);
		
		$this->tpl_vars['fonts'] = $fnts;
	}	
	
	function doParseIcons()
	{
		header('Content-type: text/plain');


		$data = $_POST['data'];

		preg_match_all("/(<svg.*?<\/svg>)/is", $data, $svgs, PREG_SET_ORDER);
		preg_match_all("/(<div class=\"icons-set__icon-title\">(.*?)<\/div>)/is", $data, $titles);

		$info = [];
		

		$titles = $titles[2];
		


		@mkdir($dir='/tmp/icons/');

		foreach ($svgs as $i => $svg) {
			$filename = $titles[$i] . '.svg';
			file_put_contents($dir . $filename, $svg[0]);
		}
		
		$info = ['files'=>count($svgs), 'titles'=>$titles, 'store_location'=>$dir];
		
		$this->log[] = $info;
		
	}

	public $doTestBackgroundRequest = ["info"=>"Tests ability to perform scripts with get-conection-close"];
	
	function doTestBackgroundRequest()
	{
		
		$test_string = GW_String_Helper::getRandString(10).' '.date('Y-m-d H:i:s');
		
		$params=[];
		
		/*
		if(isset($_GET['localhost_base']))
			$params['localhost_base']=1;
		
		if(isset($_GET['force_http']))
			$params['force_http']=1;
		 */
		$this->initModCfg();
		
		$url = Navigator::backgroundRequest($this->buildUri(false,[],['app'=>'admin']), ["act"=>'doATestBackgroundRequest','test_string'=>$test_string]);
		
		sleep(3);
		
		print_r([
			'RequestedUrl'=>$url, 
			'$this->config->backgroundTestValue'=>$this->modconfig->backgroundTestValue,
			'test_string'=>$test_string,
			'passed'=> $test_string == $this->modconfig->backgroundTestValue ? 'yes' : 'no'
		]);
		
	}
	
	
	function doATestBackgroundRequest()
	{
		$this->initModCfg();
		$this->modconfig->backgroundTestValue = $_GET['test_string'];
		echo "your test string: ".$_GET['test_string'];
		exit;
	}
	
	
	public $doTestEmail = ["info"=>"Check if mail server is correctly configured"];
	
	function doTestEmail()
	{
		$stat = mail($this->app->user->email, "test mail", "test mail body");
		
		$this->setPlainMessage("Test mail to: {$this->app->user->email} status ".  var_export($stat, true));
		$this->jump();
	}
	
	
	public $doTestPhpMailer = ['info'=>'Check mail sending with phpmailer class'];
	
	function doTestPhpMailer()
	{			
		$opts = [
		    'to'=> $this->app->user->email,
		    'subject'=>"testing php mailer",
		    'body'=>'This is test message body'
		];
		$status = GW_Mail_Helper::sendMail($opts);
		$opts['to']=implode(',', $opts['to']);
				
		$this->setMessage([
			"text"=>"mail send from ".htmlspecialchars(GW_Mail_Helper::$last_from)." to {$opts['to']} ".($status ? 'succeed':'failed'),
			'type'=>$status ? GW_MSG_SUCC : GW_MSG_ERR,
			'footer'=>$opts['error'],
			'float'=>1
		]);		
		
			
		////--------------2nd test----------------------------------
		$opts['to'] = "gwcmsmailtest@mailinator.com";
		//$opts['debug'] = 1;
		$status = GW_Mail_Helper::sendMail($opts);
		$opts['to']=implode(',', $opts['to']);
		
		$this->setMessage([
			"text"=>"2nd mail send from ".htmlspecialchars(GW_Mail_Helper::$last_from)." to {$opts['to']} ".($status ? 'succeed':'failed'),
			'type'=>$status ? GW_MSG_SUCC : GW_MSG_ERR,
			'footer'=>$opts['error'],
			'float'=>1
		]);	
	}
	

	
	public $doTestGeoip = ["info"=>"Check if geoip function working (geoip_country_code_by_name)"];
	
	function doTestGeoip()
	{
		if(function_exists('geoip_country_code_by_name')){
			$this->setPlainMessage("Feature available. Jour country ".geoip_country_code_by_name($_SERVER['REMOTE_ADDR']));
		}else{
			$this->setPlainMessage("Feature not enabled. <a target='_blank' href='http://www.beginninglinux.com/home/php/ubuntu-php-5-geo-ip'>More info</a>");
		}
		
	}
	
	

	function getDirContents($dir, &$results = array()) {
	    $files = scandir($dir);

	    foreach ($files as $key => $value) {
		$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
		if (!is_dir($path)) {
		    $results[] = $path;
		} else if ($value != "." && $value != "..") {
		   self:: getDirContents($path, $results);
		    $results[] = $path;
		}
	    }

	    return $results;
	}


 	public $doTestProjectLines = ["info"=>"calculate code size"];
	
	function doTestProjectLines()
	{
		
		$excludes = ['/.git/', '/vendor/' ,'/repository/',  '/pack/','/test/'];
		$fileslist = self::getDirContents(GW::s('DIR/ROOT'));
		$extensions = ['php','tpl','xml','js','css'];
		
		$goodlinesize = 60;
		
		foreach($fileslist as $idx => $filename){
			
			$skip = false;
			foreach($excludes as $excl){
				if(strpos($filename, $excl)!==false){
					unset($fileslist[$idx]);
					
					$skip=true;
					break;;
				}
			}
			
			if($skip)
				continue;
			
			
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$fsize = filesize($filename);
			@$num[$extension]++;
			@$filesizes[$extension]+= $fsize;
			
			if(in_array($extension, $extensions))
				$byextension[$extension][]  = [
				    str_replace(GW::s('DIR/ROOT'), '', $filename),
				    round($fsize / $goodlinesize),
				    count(explode("\n", file_get_contents($filename)))
				    ];
		}
		
		foreach($extensions as $ext){
			
			
			$info[$ext] = [
				'file_count'=> $num[$ext],
				'file_sumsize'=> GW_File_Helper::cFileSize($filesizes[$ext]),
				'lines'=>round($filesizes[$ext] / $goodlinesize)
			];
			
		}
		
		d::ldump('LINE SIZE: 60 chars');
		d::ldump($info);
		
		foreach($extensions as $ext){
			
			
			d::ldump($byextension[$ext]);
			
		}		
	}   
	
	public $doTestUserError = ["info"=>"Test user error"];
	
	function doTestUserError()
	{
		trigger_error("This is test error (E_USER_ERROR)", E_USER_ERROR);
	}	
	
	public $doTestError = ["info"=>"Test error"];
	
	function doTestError()
	{
		asdlfkjasdlkfjalsdkj();
	}	
	
	public $doTestErrorOnBackgroundRequest = ["info"=>"Test user error while requested in background (run on user:GW_USER_SYSTEM_ID)"];
	
	function doTestErrorOnBackgroundRequest()
	{
		$url = Navigator::backgroundRequest($this->buildUri(false,[],['app'=>'admin']), ["act"=>'doTestError']);	
		$this->jump();
	}
	
	public $doTestWarningOnBackgroundRequest = ["info"=>"Test user warning while requested in background (run on user:GW_USER_SYSTEM_ID)"];
	
	function doTestWarningOnBackgroundRequest()
	{
		$url = Navigator::backgroundRequest($this->buildUri(false,[],['app'=>'admin']), ["act"=>'doTestWarning']);	
		$this->jump();
	}	
	
	public $doTestWarning = ["info"=>"Test user warning"];
	
	function doTestWarning()
	{
		trigger_error("This is test error (E_USER_WARNING)", E_USER_WARNING);
	}	
	
	public $viewTestJqueryui = ["info"=>"Test jquery-ui compatability with jquery"];
	
	function viewTestJqueryui()
	{
		
	}

		
	public $viewTestHtmlarea = ["info"=>"Test CKEDITOR"];
	
	function viewTestHtmlarea()
	{
		
	}

	public $viewTestPdfGen = ["info"=>"Test dompdf"];
	
	function viewTestPdfGen()
	{
		$filename=GW::s('DIR/SYS_REPOSITORY').'testpdfhtml.html';
		
		if($_POST)
		{
			//d::dumpas($_POST);
			file_put_contents($filename, $_POST['item']['htmlcontents']);
		}
		
		$this->tpl_vars['filecontents'] = @file_get_contents($filename);
	}		
	
	function doGenPdf()
	{
		$filename=GW::s('DIR/SYS_REPOSITORY').'testpdfhtml.html';
		
		$pdf=GW_html2pdf_Helper::convert(file_get_contents($filename), false);
		header("Content-type:application/pdf");
		header("Content-Disposition:inline;filename=test.pdf");
		die($pdf);		
	}
	
	function doSwitchEnvironment()
	{		
		$replace_what = GW::s("SITE_URL");
		
		$current_env = GW::s('PROJECT_ENVIRONMENT') ==  GW_ENV_DEV ? 'DEV':'PROD';
		
		
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV)
		{
			$dest = GW_ENV_PROD;	
		}else{
			$dest = GW_ENV_DEV;
		}
		
		$dest_env = $dest ==  GW_ENV_DEV ? 'DEV':'PROD';
			
		initEnviroment($dest);
		$replace_to = GW::s("SITE_URL");
		
		//if($replace_what == $replace_to)
		//{
		//	d::dumpas("CONFIG WRONG: Replace from: $replace_what | Replace to: $replace_to");
		//}
		//DEBUG : UNCOMMENT THIS:::
		$base = Navigator::getBase(true);
					
		$url = $_GET['uri'];
		$url = str_replace($base,'', $url);
		
		//$url  = str_replace("https://", "http://", $_GET['uri']);//redirect should solve return to https protocol
		
		
		
		//$newurl = str_replace($replace_what, $replace_to, $url);
		$newurl = $replace_to . $url;
		
		//d::dumpas([$url, $replace_to,$newurl]);
		
		//d::dumpas(['current_env'=>GW::s('PROJECT_ENVIRONMENT'), "destination_env"=>$dest, 'replace_what'=>$replace_what,'replace_to'=>$replace_to,'result'=>$newurl]);
		

		if($newurl == $_GET['uri'])
		{
			d::ldump([
			    'REQUEST_URI'=>$_SERVER['REQUEST_URI'], 
			    'current_env'=>$current_env,
			    'dest_env'=>$dest_env,
			    'src_url'=>$_GET['uri'],
			    'dst_url'=>$newurl,
			]);
			d::dumpas("Replace failed CONFIG WRONG: Replace from: $replace_what | Replace to: $replace_to");
		}		
		
			
		
		header("Location: $newurl");
		exit;
	}
	
	function doPullProductionDB()
	{
		//d::dumpas('testas');
		//tiesiai galima butu atiduot variantas
		//list($dbuser, $dbpass, $host, $database, $port) = GW_DB::parse_uphd(GW::s('DB/UPHD'));
		//$extra = "";
		//echo shell_exec("mysqldump --force --opt --add-drop-database $extra --user=$dbuser -p{$dbpass} $database");
		
		
		$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
		$sudouser = 'wdm';
		$level = ['light'=>'light','full'=>'full'][$_GET['level'] ?? 'full'];
		
		$res=shell_exec($cmd="sudo -S -u $sudouser /usr/bin/php $path pulldb $level  2>&1");
		
		$this->setMessage("<pre>".$res."</pre>");
		
		if(GW::s('PROJECT_NAME')=='tometa')
			$url=Navigator::backgroundRequest('admin/lt/system/dbqueries?act=doExecuteQuery&id=5');
				
		header("Location: ".$_GET['uri']);
		exit;		
	}
	
	
	function initModCfg()
	{
		$this->modconfig = new GW_Config_FS('system__tools');
	}	
	
	function __eventBeforeConfig($cfg)
	{
		$tables = GW::db()->fetch_assoc("SHOW TABLES");
		
		$this->options['tables'] = array_keys($tables);
		
		//d::dumpas(json_decode($cfg->sync_ignore_tables_1));
	}
	
	function __eventBeforeSaveConfig($vals)
	{
		
		//d::dumpas($_REQUEST);
	}
	
}
