<?php


chdir(__DIR__.'/../../');
include __DIR__.'/../../init_basic.php'; 

define('SUDOGATE_ERROR_PUSH_USER_ID', 9);

function sudogate_arg_value($args, $name, $default = null)
{
	foreach ($args as $arg) {
		if (strpos($arg, $name . '=') === 0)
			return substr($arg, strlen($name) + 1);
	}
	
	return $default;
}

function sudogate_notify_error($message, $context = [])
{
	static $sent = false;
	
	if ($sent)
		return;
	
	$sent = true;
	
	try {
		$host = trim((string)(GW::s('MAIN_HOST') ?: parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST)));
		$url = $host ? ('https://' . $host . '/admin/lt/system/tools') : '';
		$body = trim((string)$message);
		
		if ($context)
			$body .= "\n" . trim(json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		
		$payload = [
			'title' => GW::s('PROJECT_NAME') . ' sudogate error',
			'body' => mb_substr($body, 0, 500),
			'tag' => 'sudogate-error-' . md5($body),
			'data' => ['url' => $url],
		];
		
		GW_Android_Push_Notif::pushWeb(SUDOGATE_ERROR_PUSH_USER_ID, $payload);
	} catch (Throwable $e) {
	}
}

function sudogate_notify_user($userId, $title, $body, $ok = true)
{
	$userId = (int)$userId;
	
	if (!$userId)
		return;
	
	try {
		$host = trim((string)(GW::s('MAIN_HOST') ?: parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST)));
		$url = $host ? ('https://' . $host . '/admin/lt/sitemap/sites') : '';
		$body = trim((string)$body);
		
		$payload = [
			'title' => $title,
			'body' => mb_substr($body, 0, 500),
			'tag' => 'sudogate-sites-deploy-' . ($ok ? 'ok' : 'fail'),
			'data' => ['url' => $url],
		];
		
		GW_Android_Push_Notif::pushWeb($userId, $payload);
	} catch (Throwable $e) {
	}
}

function sudogate_fail($message, $context = [])
{
	sudogate_notify_error($message, $context);
	die(rtrim((string)$message) . "\n");
}

function sudogate_has_flag($args, $flag)
{
	return in_array($flag, $args, true);
}

function sudogate_exec($cmd)
{
	echo $cmd . "\n";
	$out = [];
	$code = 0;
	exec($cmd . ' 2>&1', $out, $code);
	echo implode("\n", $out) . "\n";
	
	if ($code !== 0)
		sudogate_fail("Command failed: $cmd", ['exit_code' => $code, 'output' => implode("\n", $out)]);
}

function sudogate_exec_capture($cmd)
{
	$out = [];
	$code = 0;
	exec($cmd . ' 2>&1', $out, $code);
	
	return [
		'cmd' => $cmd,
		'code' => $code,
		'output' => implode("\n", $out),
	];
}

register_shutdown_function(function () {
	$error = error_get_last();
	
	if (!$error)
		return;
	
	if (!in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true))
		return;
	
	sudogate_notify_error($error['message'], [
		'file' => $error['file'] ?? null,
		'line' => $error['line'] ?? null,
	]);
});

function sudogate_update_http_conf($args)
{
	$root = rtrim(GW::s('DIR/ROOT'), '/') . '/';
	$source = $root . 'deploy/http.conf';
	$builder = $root . 'deploy/http_conf.php';
	$target = sudogate_arg_value($args, '--target');
	
	if (!$target) {
		$confName = GW::s('MAIN_HOST') ?: parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST) ?: GW::s('PROJECT_NAME');
		$target = '/etc/apache2/sites-enabled/' . $confName . '.conf';
	}
	
	$target = preg_replace('~/{2,}~', '/', $target);
	$allowedPrefixes = [
		'/etc/apache2/sites-available/',
		'/etc/apache2/sites-enabled/',
		'/etc/apache2/conf-available/',
	];
	
	$allowed = false;
	foreach ($allowedPrefixes as $prefix) {
		if (strpos($target, $prefix) === 0) {
			$allowed = true;
			break;
		}
	}
	
	if (!$allowed)
		sudogate_fail("Target path is not allowed: $target");
	
	if (is_file($builder)) {
		require_once $builder;
		
		if (!function_exists('gw_deploy_build_http_conf'))
			sudogate_fail("Builder function not found: gw_deploy_build_http_conf");
		
		$generated = gw_deploy_build_http_conf();
		$source = GW::s('DIR/TEMP') . 'deploy_http.conf';
		if (file_put_contents($source, $generated) === false)
			sudogate_fail("Generated config write failed: $source");
		
		echo "Generated: $source\n";
	} else {
		if (!is_file($source))
			sudogate_fail("Source not found: $source");
		
		if (!is_readable($source))
			sudogate_fail("Source not readable: $source");
	}
	
	$targetDir = dirname($target);
	if (!is_dir($targetDir))
		sudogate_fail("Target directory does not exist: $targetDir");
	
	$oldHash = is_file($target) ? sha1_file($target) : null;
	$newHash = sha1_file($source);
	
	if ($oldHash && $oldHash === $newHash) {
		echo "No changes: $target\n";
		return;
	}
	
	$backup = null;
	if (is_file($target)) {
		$backup = $target . '.bak-' . date('Ymd-His');
		if (!copy($target, $backup))
			sudogate_fail("Backup failed: $backup");
		echo "Backup: $backup\n";
	}
	
	if (!copy($source, $target)) {
		if ($backup)
			copy($backup, $target);
		
		sudogate_fail("Copy failed: $source -> $target");
	}
	
	echo "Updated: $target\n";
	
	$testCmd = 'apache2ctl configtest 2>&1';
	$out = shell_exec($testCmd);
	echo $testCmd . "\n" . $out;
	
	if (strpos((string)$out, 'Syntax OK') === false) {
		if ($backup && copy($backup, $target))
			echo "Restored backup: $backup\n";
		
		sudogate_fail("Apache configtest failed", ['output' => trim((string)$out)]);
	}
}

function sudogate_setup_ssl($args)
{
	$root = rtrim(GW::s('DIR/ROOT'), '/') . '/';
	$builder = $root . 'deploy/http_conf.php';
	$only = sudogate_arg_value($args, '--cert');
	$force = sudogate_has_flag($args, '--force');
	$dryRun = sudogate_has_flag($args, '--dry-run');
	
	if (!is_file($builder))
		sudogate_fail("HTTP config builder not found: $builder");
	
	require_once $builder;
	
	if (!function_exists('gw_deploy_http_conf_certbot_configs'))
		sudogate_fail("Builder function not found: gw_deploy_http_conf_certbot_configs");
	
	$configs = gw_deploy_http_conf_certbot_configs();
	
	if (!$configs) {
		echo "No SSL certificates configured for this environment\n";
		return;
	}
	
	foreach ($configs as $cfg) {
		if ($only && $cfg['name'] !== $only)
			continue;
		
		$certOk = is_file($cfg['cert_file']) && is_file($cfg['key_file']);
		
		if (isset($cfg['managed']) && !$cfg['managed']) {
			echo ($certOk ? "Certificate exists" : "Certificate missing") . " and is unmanaged by this project: {$cfg['name']}\n";
			if (!$certOk)
				sudogate_fail("Unmanaged certificate is missing: {$cfg['name']}", $cfg);
			continue;
		}
		
		if ($certOk && !$force) {
			echo "Certificate exists: {$cfg['name']}\n";
			continue;
		}
		
		$cmd = [
			'certbot',
			'certonly',
			'--manual',
			'--preferred-challenges',
			'dns',
			'--manual-auth-hook',
			'php8.4 /root/.iv-le/iv-certbot.php auth',
			'--manual-cleanup-hook',
			'php8.4 /root/.iv-le/iv-certbot.php cleanup',
			'--non-interactive',
			'--agree-tos',
			'--register-unsafely-without-email',
			'--expand',
			'--cert-name',
			$cfg['name'],
		];
		
		if ($dryRun)
			$cmd[] = '--dry-run';
		
		foreach ($cfg['domains'] as $domain) {
			$cmd[] = '-d';
			$cmd[] = $domain;
		}
		
		sudogate_exec(implode(' ', array_map('escapeshellarg', $cmd)));
	}
}

function sudogate_update_http_conf_and_ssl($args)
{
	$notifyUserId = (int)sudogate_arg_value($args, '--notify-user', 0);
	$self = __FILE__;
	$php = GW::s('PHP_CLI_LOCATION') ?: '/usr/bin/php';
	$commands = [
		escapeshellcmd($php) . ' ' . escapeshellarg($self) . ' setup-ssl',
		escapeshellcmd($php) . ' ' . escapeshellarg($self) . ' update-http-conf',
	];
	
	$ok = true;
	$chunks = [];
	
	foreach ($commands as $cmd) {
		$res = sudogate_exec_capture($cmd);
		$chunks[] = '$ ' . $res['cmd'] . "\n" . $res['output'];
		
		if ($res['code'] !== 0) {
			$ok = false;
			break;
		}
	}
	
	$output = trim(implode("\n\n", $chunks));
	echo $output . "\n";
	
	if ($ok) {
		sudogate_notify_user(
			$notifyUserId,
			GW::s('PROJECT_NAME') . ' http.conf/SSL updated',
			"SSL certs checked and http.conf updated successfully.",
			true
		);
		return;
	}
	
	sudogate_notify_error('http.conf/SSL update failed', ['output' => $output]);
	sudogate_notify_user(
		$notifyUserId,
		GW::s('PROJECT_NAME') . ' http.conf/SSL failed',
		$output,
		false
	);
	exit(1);
}



switch($argv[1]){
	case 'update-http-conf-and-ssl':
		sudogate_update_http_conf_and_ssl(array_slice($argv, 2));
	break;
	
	case 'update-http-conf':
		sudogate_update_http_conf(array_slice($argv, 2));
	break;
	
	case 'setup-ssl':
		sudogate_setup_ssl(array_slice($argv, 2));
	break;
	
	case 'pulldb':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			$level = $argv[2];
			$cfg = new GW_Config_FS('system__tools');
			$tables=json_decode($cfg->get("{$level}_sync_ignore_tables"),true);
			
			$cmdargs="";
			if($tables)
				$cmdargs="--exclude=".implode(',', $tables);
			
			$cmd =  "php ".__DIR__."/db_sync_whole.php $cmdargs 2>&1";
			echo $cmd."\n";			
			echo shell_exec($cmd);
		}else{
			echo "Only on dev";
		}
	break;
	
	case 'recoverdb':
		$backupfolder = $argv[2];
		$cmd =  "php ".__DIR__."/db_sync_whole.php --recoverdb=$backupfolder 2>&1";
		echo $cmd."\n";			
		echo shell_exec($cmd);		
	break;
	

	case 'test_sync_with_prod':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_TEST){
			echo shell_exec($cmd="sudo /usr/bin/php ".__DIR__."/test_sync_with_prod.php 2>&1");
		}
	break;

	case 'sync':
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			echo shell_exec("php ".GW::s('DIR/ROOT')."/update.php -web 2>&1");
		}else{
			echo "Only on dev";
		}
	break;	
	case 'writelang':
		$id = $argv[2];
				
		$t = new GW_Timer;
		
		$rdir =& GW::s('DIR');
		$dir =& $rdir['SITE'];

		$rdir['ADMIN']['ROOT']=$rdir['APPLICATIONS'].'admin/';
		$rdir['ADMIN']['MODULES']=$rdir['ADMIN']['ROOT'].'modules/';
		$rdir['ADMIN']['LANG']=$rdir['ADMIN']['ROOT'].'lang/';
		
		

		$rdir['AUTOLOAD'][] = @$dir['LIB'];
		$rdir['AUTOLOAD_RECURSIVE'] = $rdir['ADMIN']['MODULES'];	
		
		
		$user = $argv[3];
		echo "user is $user;\n";
		
		GW_Lang::$ln = 'en';
		GW_Lang::$app = "ADMIN";
		GW_Lang::$langf_dir = GW::s("DIR/APPLICATIONS") . 'ADMIN' . '/lang/';
		
		$lf = new GW_Lang_File($id);
		$lf->load();
		
		if(!$lf->newexists)
			die('temp not exists');
		
		if(GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV){
			$lf->writeToOriginal();	
			
		
		}else{
			$projdir = GW::s('PROJECT_NAME');
			$projrepos = GW::s('PROJECT_CODE_REPOS');
			$tmpdir = "/tmp/code_adj_{$projdir}/";
			
			if(!file_exists($tmpdir.'index.php')){
				mkdir($tmpdir);
				passthru("cd $tmpdir && git clone $projrepos .");
			}
			
			
			$dest = str_replace(GW::s('DIR/ROOT'),$tmpdir,$lf->filename);
			
			
			chdir($tmpdir);
			passthru('git pull');
			
			$lf->writeToOriginal($dest);
			
			passthru("git add *.xml");
			passthru("git commit -m 'translations from $user'");
			passthru("git push");
			echo "Speed is {$t->stop()} secs\n";
		}
	break;
}
