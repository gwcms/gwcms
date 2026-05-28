<?php

function gw_deploy_http_conf_escape($value)
{
	return str_replace('"', '\"', (string)$value);
}

function gw_deploy_http_conf_setting($key, $default = null)
{
	$value = GW::s($key);
	
	return $value === null || $value === '' ? $default : $value;
}

function gw_deploy_http_conf_bool_setting($key, $default = false)
{
	$value = gw_deploy_http_conf_setting($key, $default ? 1 : 0);
	
	if (is_bool($value))
		return $value;
	
	return in_array(strtolower((string)$value), ['1', 'true', 'yes', 'on'], true);
}

function gw_deploy_http_conf_main_host()
{
	$host = trim((string)GW::s('MAIN_HOST'));
	
	if (!$host)
		$host = parse_url((string)GW::s('SITE_URL'), PHP_URL_HOST);
	
	$host = strtolower(trim((string)$host));
	$host = preg_replace('/:\d+$/', '', $host);
	
	if (!$host)
		$host = GW::s('PROJECT_NAME') . '.localhost';
	
	return $host;
}

function gw_deploy_http_conf_alias_lines($aliases, $indent = "\t")
{
	$out = '';
	foreach (array_values(array_unique((array)$aliases)) as $alias)
		$out .= $indent . 'ServerAlias ' . $alias . "\n";
	
	return $out;
}

function gw_deploy_http_conf_split_hosts($hosts)
{
	$out = [];
	foreach (preg_split('/[,\s]+/', (string)$hosts) as $host) {
		$host = strtolower(trim($host));
		$host = trim($host, " \t\n\r\0\x0B,;");
		
		if ($host === '' || $host === '*')
			continue;
		
		$out[] = $host;
	}
	
	return $out;
}

function gw_deploy_http_conf_add_host(&$hosts, $host)
{
	$host = strtolower(trim((string)$host));
	
	if ($host === '' || in_array($host, $hosts, true))
		return;
	
	$hosts[] = $host;
}

function gw_deploy_http_conf_add_localhost_aliases($hosts)
{
	$out = [];
	
	foreach ($hosts as $host) {
		gw_deploy_http_conf_add_host($out, $host);
		
		if (substr($host, -10) !== '.localhost')
			gw_deploy_http_conf_add_host($out, $host . '.localhost');
	}
	
	return $out;
}

function gw_deploy_http_conf_add_only_localhost_aliases($hosts)
{
	$out = [];
	
	foreach ($hosts as $host) {
		if (substr($host, -10) !== '.localhost')
			gw_deploy_http_conf_add_host($out, $host . '.localhost');
	}
	
	return $out;
}

function gw_deploy_http_conf_is_dev_host($host)
{
	return substr($host, -4) === '.adb' || $host === 'adb';
}

function gw_deploy_http_conf_is_apex_host($host)
{
	if (strpos($host, '*') !== false || substr($host, -10) === '.localhost')
		return false;
	
	if (strpos($host, 'www.') === 0)
		return false;
	
	return substr_count($host, '.') === 1;
}

function gw_deploy_http_conf_is_main_host_domain($host, $mainHost)
{
	return $host === $mainHost || substr($host, -(strlen($mainHost) + 1)) === '.' . $mainHost;
}

function gw_deploy_http_conf_is_test_env()
{
	return defined('GW_ENV_TEST') && (int)GW::s('PROJECT_ENVIRONMENT') === GW_ENV_TEST;
}

function gw_deploy_http_conf_is_dev_env()
{
	return defined('GW_ENV_DEV') && (int)GW::s('PROJECT_ENVIRONMENT') === GW_ENV_DEV;
}

function gw_deploy_http_conf_dev_ssl_enabled()
{
	return gw_deploy_http_conf_bool_setting('DEPLOY_HTTP/DEV_SSL', false);
}

function gw_deploy_http_conf_test_suffix()
{
	return trim((string)gw_deploy_http_conf_setting('DEPLOY_HTTP/TEST_SUFFIX', '1.voro.lt'), '.');
}

function gw_deploy_http_conf_xweb_suffix()
{
	return trim((string)gw_deploy_http_conf_setting('DEPLOY_HTTP/XWEB_SUFFIX', '1.xweb.lt'), '.');
}

function gw_deploy_http_conf_is_xweb_host($host)
{
	$suffix = gw_deploy_http_conf_xweb_suffix();

	return $host === $suffix || substr($host, -(strlen($suffix) + 1)) === '.' . $suffix;
}

function gw_deploy_http_conf_is_xweb_alias($host)
{
	$host = preg_replace('/\.localhost$/', '', strtolower(trim((string)$host)));

	return gw_deploy_http_conf_is_xweb_host($host);
}

function gw_deploy_http_conf_without_xweb_aliases($hosts)
{
	$out = [];
	foreach ($hosts as $host) {
		if (!gw_deploy_http_conf_is_xweb_alias($host))
			gw_deploy_http_conf_add_host($out, $host);
	}

	return $out;
}

function gw_deploy_http_conf_test_host($host)
{
	$host = strtolower(trim((string)$host));
	$host = preg_replace('/:\d+$/', '', $host);
	$host = preg_replace('/\.localhost$/', '', $host);
	$host = preg_replace('/\.1\.voro\.lt$/', '', $host);
	
	return str_replace('.', '-', $host) . '.' . gw_deploy_http_conf_test_suffix();
}

function gw_deploy_http_conf_add_test_aliases($hosts)
{
	$out = [];
	foreach ($hosts as $host)
		gw_deploy_http_conf_add_host($out, gw_deploy_http_conf_test_host($host));
	
	return $out;
}

function gw_deploy_http_conf_db_site_hosts()
{
	$hosts = [];
	
	try {
		GW::db();
		$rows = GW::db()->fetch_rows("SELECT hosts FROM gw_sites WHERE active=1 AND hosts!='' ORDER BY id");
		
		foreach ($rows as $row) {
			foreach (gw_deploy_http_conf_split_hosts($row['hosts'] ?? '') as $host)
				gw_deploy_http_conf_add_host($hosts, $host);
		}
	} catch (Throwable $e) {
	}
	
	return $hosts;
}

function gw_deploy_http_conf_fallback_site_hosts()
{
	$hosts = [];
	$cfg = GW::s('MULTISITE_CFG') ?: [];
	
	foreach ($cfg as $site) {
		foreach (array_keys((array)($site['hosts'] ?? [])) as $host)
			gw_deploy_http_conf_add_host($hosts, $host);
	}
	
	return $hosts;
}

function gw_deploy_http_conf_multisite_hosts()
{
	$hosts = gw_deploy_http_conf_db_site_hosts();
	
	if (!$hosts)
		$hosts = gw_deploy_http_conf_fallback_site_hosts();
	
	return $hosts;
}

function gw_deploy_http_conf_xweb_hosts()
{
	$hosts = [];
	foreach (gw_deploy_http_conf_multisite_hosts() as $host) {
		if (gw_deploy_http_conf_is_xweb_host($host))
			gw_deploy_http_conf_add_host($hosts, $host);
	}

	return $hosts;
}

function gw_deploy_http_conf_localhost_hosts()
{
	$hosts = [gw_deploy_http_conf_main_host()];

	foreach (gw_deploy_http_conf_multisite_hosts() as $host) {
		if (gw_deploy_http_conf_is_dev_host($host))
			continue;

		gw_deploy_http_conf_add_host($hosts, $host);

		if (gw_deploy_http_conf_is_apex_host($host))
			gw_deploy_http_conf_add_host($hosts, 'www.' . $host);
	}

	return gw_deploy_http_conf_add_localhost_aliases($hosts);
}

function gw_deploy_http_conf_cert_file($certName, $file)
{
	return '/etc/letsencrypt/live/' . $certName . '/' . $file;
}

function gw_deploy_http_conf_cert_file_usable($file)
{
	return is_file($file) && filesize($file) > 0;
}

function gw_deploy_http_conf_ws($opts, $indent = "\t")
{
	if (empty($opts['enabled']))
		return '';
	
	$host = $opts['host'] ?? (GW::s('CHATWS/HOST') ?: '127.0.0.1');
	$port = (int)($opts['port'] ?? (GW::s('CHATWS/PORT') ?: 9051));
	$path = $opts['path'] ?? (GW::s('CHATWS/PATH') ?: '/ws');
	$targetPath = $opts['target_path'] ?? $path;
	$extra = trim((string)($opts['extra'] ?? ''));
	$extra = $extra ? ' ' . $extra : '';
	
	return "\n"
		. $indent . "ProxyPreserveHost On\n"
		. $indent . "ProxyRequests Off\n"
		. $indent . "ProxyPass {$path} ws://{$host}:{$port}{$targetPath}{$extra}\n"
		. $indent . "ProxyPassReverse {$path} ws://{$host}:{$port}{$targetPath}\n";
}

function gw_deploy_http_conf_common_blocks($site, $indent = "\t")
{
	$root = gw_deploy_http_conf_escape($site['document_root']);
	$phpFpmSock = gw_deploy_http_conf_escape($site['php_fpm_sock'] ?? '/run/php/php8.4-fpm.sock');
	$repositoryDir = gw_deploy_http_conf_escape($site['repository_dir'] ?? rtrim($site['document_root'], '/') . '/repository');
	
	return "\n"
		. $indent . "<Directory \"{$root}\">\n"
		. $indent . "\tOptions FollowSymLinks\n"
		. $indent . "\tAllowOverride All\n\n"
		. $indent . "\t<FilesMatch \"\\.php$\">\n"
		. $indent . "\t\tSetHandler \"proxy:unix:{$phpFpmSock}|fcgi://localhost\"\n"
		. $indent . "\t</FilesMatch>\n"
		. $indent . "</Directory>\n\n"
		. $indent . "<Directory \"{$repositoryDir}\">\n"
		. $indent . "\t<FilesMatch \"\\.php$\">\n"
		. $indent . "\t\tSetHandler None\n"
		. $indent . "\t</FilesMatch>\n"
		. $indent . "\tRequire all granted\n"
		. $indent . "</Directory>\n\n"
		. $indent . "<LocationMatch \"^/(pingas|bukle)$\">\n"
		. $indent . "\tRewriteEngine Off\n"
		. $indent . "\tSetHandler \"proxy:unix:{$phpFpmSock}|fcgi://localhost\"\n"
		. $indent . "</LocationMatch>\n";
}

function gw_deploy_http_conf_vhost($site, $port)
{
	$isSsl = (int)$port === 443;
	$root = gw_deploy_http_conf_escape($site['document_root']);
	$name = $site['server_name'];
	$aliases = $site[$isSsl ? 'ssl_aliases' : 'aliases'] ?? $site['aliases'] ?? [];
	$ws = $site['ws'] ?? [];
	$wsEnabled = $ws[$isSsl ? 'https' : 'http'] ?? false;
	$wsOpts = $ws + ['enabled' => $wsEnabled];
	$logPrefix = $site['log_prefix'];
	
	$out = "<VirtualHost *:{$port}>\n";
	$out .= "\tServerName {$name}\n";
	$out .= gw_deploy_http_conf_alias_lines($aliases);
	$out .= "\n\tDocumentRoot \"{$root}\"\n";
	
	if ($isSsl) {
		$out .= "\n\tSSLEngine On\n";
		$out .= "\tSSLUseStapling Off\n";
		$out .= "\tSSLCertificateFile \"" . gw_deploy_http_conf_escape($site['ssl']['cert']) . "\"\n";
		$out .= "\tSSLCertificateKeyFile \"" . gw_deploy_http_conf_escape($site['ssl']['key']) . "\"\n";
		
		if (!empty($site['ssl']['chain']))
			$out .= "\tSSLCertificateChainFile \"" . gw_deploy_http_conf_escape($site['ssl']['chain']) . "\"\n";
	}
	
	$out .= gw_deploy_http_conf_ws($wsOpts);
	$out .= gw_deploy_http_conf_common_blocks($site);
	$out .= "\n\tErrorLog \${APACHE_LOG_DIR}/{$logPrefix}-error.log\n";
	$out .= "\tCustomLog \${APACHE_LOG_DIR}/{$logPrefix}-access.log combined\n";
	$out .= "</VirtualHost>\n";
	
	return $out;
}

function gw_deploy_http_conf_sites()
{
	$mainHost = gw_deploy_http_conf_main_host();
	$isTestEnv = gw_deploy_http_conf_is_test_env();
	$isDevEnv = gw_deploy_http_conf_is_dev_env();
	$documentRoot = rtrim(GW::s('DEPLOY_DIR') ?: GW::s('DIR/ROOT'), '/');
	$wsHost = GW::s('CHATWS/HOST') ?: '127.0.0.1';
	$wsPort = (int)(GW::s('CHATWS/PORT') ?: 9051);
	$wsPath = GW::s('CHATWS/PATH') ?: '/ws';
	$wildcardEnabled = !$isTestEnv && !$isDevEnv && gw_deploy_http_conf_bool_setting('DEPLOY_HTTP/WILDCARD_ENABLED', false);
	$mainCertName = $isTestEnv
		? gw_deploy_http_conf_setting('DEPLOY_HTTP/TEST_CERT_NAME', 'voro1wildcard')
		: gw_deploy_http_conf_setting('DEPLOY_HTTP/MAIN_CERT_NAME', $mainHost);
	$wildcardCertName = gw_deploy_http_conf_setting('DEPLOY_HTTP/WILDCARD_CERT_NAME', str_replace('.', '', $mainHost) . 'wildcard');
	$dbHosts = gw_deploy_http_conf_multisite_hosts();
	$mainHosts = [];
	$mainSslHosts = [];
	$xwebHosts = [];
	$originalHosts = [$mainHost];
	
	foreach ($dbHosts as $host) {
		if ($host === $mainHost) {
			gw_deploy_http_conf_add_host($mainHosts, $host . '.localhost');
			gw_deploy_http_conf_add_host($originalHosts, $host);
			continue;
		}
		
		if (gw_deploy_http_conf_is_dev_host($host))
			continue;

		if (gw_deploy_http_conf_is_xweb_host($host)) {
			gw_deploy_http_conf_add_host($xwebHosts, $host);
			gw_deploy_http_conf_add_host($originalHosts, $host);
			continue;
		}

		if ($wildcardEnabled && gw_deploy_http_conf_is_main_host_domain($host, $mainHost))
			continue;
		
		gw_deploy_http_conf_add_host($mainSslHosts, $host);
		gw_deploy_http_conf_add_host($mainHosts, $host);
		gw_deploy_http_conf_add_host($originalHosts, $host);
		
		if (gw_deploy_http_conf_is_apex_host($host)) {
			gw_deploy_http_conf_add_host($mainSslHosts, 'www.' . $host);
			gw_deploy_http_conf_add_host($mainHosts, 'www.' . $host);
			gw_deploy_http_conf_add_host($originalHosts, 'www.' . $host);
		}
	}
	
	if ($isTestEnv) {
		$serverName = gw_deploy_http_conf_test_host($mainHost);
		$mainHosts = array_merge(gw_deploy_http_conf_add_test_aliases($originalHosts), gw_deploy_http_conf_add_only_localhost_aliases($originalHosts));
		$mainSslHosts = gw_deploy_http_conf_add_test_aliases($originalHosts);
	} elseif ($isDevEnv) {
		$serverName = $mainHost . '.localhost';
		$mainHosts = gw_deploy_http_conf_add_localhost_aliases($originalHosts);
		$mainSslHosts = [];
	} else {
		$serverName = $mainHost;
		$mainHosts = gw_deploy_http_conf_add_localhost_aliases($mainHosts);
	}

	$mainCertFile = gw_deploy_http_conf_cert_file($mainCertName, 'cert.pem');
	$mainSslAvailable = gw_deploy_http_conf_cert_file_usable($mainCertFile);
	$devSslEnabled = $isDevEnv && gw_deploy_http_conf_dev_ssl_enabled();

	if ($isDevEnv && ($mainSslAvailable || $devSslEnabled))
		$mainSslHosts = gw_deploy_http_conf_without_xweb_aliases($mainHosts);

	$common = [
		'document_root' => $documentRoot,
		'php_fpm_sock' => '/run/php/php8.4-fpm.sock',
		'ws' => [
			'host' => $wsHost,
			'port' => $wsPort,
			'path' => $wsPath,
			'target_path' => '/',
		],
	];
	
	$sites = [];
	
	if ($wildcardEnabled) {
		$sites[] = [
			'server_name' => 'wildcard.' . $mainHost,
			'aliases' => ['*.' . $mainHost, '*.' . $mainHost . '.localhost'],
			'ssl_aliases' => ['*.' . $mainHost, '*.' . $mainHost . '.localhost'],
			'log_prefix' => GW::s('PROJECT_NAME') . '-wildcard',
			'ssl' => [
				'cert' => gw_deploy_http_conf_cert_file($wildcardCertName, 'fullchain.pem'),
				'key' => gw_deploy_http_conf_cert_file($wildcardCertName, 'privkey.pem'),
			],
			'ws' => [
				'http' => false,
				'https' => true,
				'host' => $wsHost,
				'port' => $wsPort,
				'path' => $wsPath,
				'target_path' => $wsPath,
				'extra' => 'retry=0 connectiontimeout=1 timeout=3600 nocanon',
			],
		] + $common;
	}
	
	$sites[] =
		[
			'server_name' => $serverName,
			'aliases' => $mainHosts,
			'ssl_aliases' => $mainSslHosts,
			'log_prefix' => GW::s('PROJECT_NAME'),
			'ports' => $isDevEnv && !$mainSslAvailable && !$devSslEnabled ? [80] : [443, 80],
			'ssl' => [
				'cert' => $mainCertFile,
				'key' => gw_deploy_http_conf_cert_file($mainCertName, 'privkey.pem'),
				'chain' => gw_deploy_http_conf_cert_file($mainCertName, 'chain.pem'),
			],
			'ws' => [
				'http' => true,
				'https' => true,
				'host' => $wsHost,
				'port' => $wsPort,
				'path' => $wsPath,
				'target_path' => '/',
			],
		] + $common;
	
	if ($xwebHosts) {
		$xwebCertName = gw_deploy_http_conf_setting('DEPLOY_HTTP/XWEB_CERT_NAME', gw_deploy_http_conf_xweb_suffix());
		$xwebServerName = $xwebHosts[0];
		$xwebCertFile = gw_deploy_http_conf_cert_file($xwebCertName, 'cert.pem');

		if (gw_deploy_http_conf_cert_file_usable($xwebCertFile) || $devSslEnabled) {
			$sites[] = [
				'server_name' => $xwebServerName,
				'aliases' => $xwebHosts,
				'ssl_aliases' => $xwebHosts,
				'log_prefix' => GW::s('PROJECT_NAME') . '-xweb',
				'ports' => $isDevEnv ? [443] : [443, 80],
				'ssl' => [
					'cert' => $xwebCertFile,
					'key' => gw_deploy_http_conf_cert_file($xwebCertName, 'privkey.pem'),
					'chain' => gw_deploy_http_conf_cert_file($xwebCertName, 'chain.pem'),
				],
				'ws' => [
					'http' => true,
					'https' => true,
					'host' => $wsHost,
					'port' => $wsPort,
					'path' => $wsPath,
					'target_path' => '/',
				],
			] + $common;
		}
	}

	return $sites;
}

function gw_deploy_http_conf_certbot_configs()
{
	$out = [];
	$mainHost = gw_deploy_http_conf_main_host();
	$isTestEnv = gw_deploy_http_conf_is_test_env();
	$isDevEnv = gw_deploy_http_conf_is_dev_env();

	if ($isDevEnv && !gw_deploy_http_conf_dev_ssl_enabled())
		return [];
	
	$mainCertName = $isTestEnv
		? gw_deploy_http_conf_setting('DEPLOY_HTTP/TEST_CERT_NAME', 'voro1wildcard')
		: gw_deploy_http_conf_setting('DEPLOY_HTTP/MAIN_CERT_NAME', $mainHost);
	$wildcardCertName = gw_deploy_http_conf_setting('DEPLOY_HTTP/WILDCARD_CERT_NAME', str_replace('.', '', $mainHost) . 'wildcard');
	$wildcardEnabled = !$isTestEnv && gw_deploy_http_conf_bool_setting('DEPLOY_HTTP/WILDCARD_ENABLED', false);
	$xwebCertName = gw_deploy_http_conf_setting('DEPLOY_HTTP/XWEB_CERT_NAME', gw_deploy_http_conf_xweb_suffix());
	
	if ($isTestEnv) {
		$suffix = gw_deploy_http_conf_test_suffix();
		return [[
			'name' => $mainCertName,
			'domains' => [$suffix, '*.' . $suffix],
			'manual_dns' => true,
			'managed' => false,
			'cert_file' => gw_deploy_http_conf_cert_file($mainCertName, 'cert.pem'),
			'key_file' => gw_deploy_http_conf_cert_file($mainCertName, 'privkey.pem'),
		]];
	}
	
	if ($wildcardEnabled) {
		$out[] = [
			'name' => $wildcardCertName,
			'domains' => [$mainHost, '*.' . $mainHost],
			'manual_dns' => true,
			'cert_file' => gw_deploy_http_conf_cert_file($wildcardCertName, 'fullchain.pem'),
			'key_file' => gw_deploy_http_conf_cert_file($wildcardCertName, 'privkey.pem'),
		];
	}

	if (gw_deploy_http_conf_xweb_hosts()) {
		$out[] = [
			'name' => $xwebCertName,
			'domains' => [gw_deploy_http_conf_xweb_suffix(), '*.' . gw_deploy_http_conf_xweb_suffix()],
			'manual_dns' => true,
			'cert_file' => gw_deploy_http_conf_cert_file($xwebCertName, 'cert.pem'),
			'key_file' => gw_deploy_http_conf_cert_file($xwebCertName, 'privkey.pem'),
		];
	}
	
	foreach (gw_deploy_http_conf_sites() as $site) {
		if (gw_deploy_http_conf_is_xweb_host($site['server_name'] ?? ''))
			continue;

		$isMainSite = ($site['server_name'] ?? '') === $mainHost;
		$isDevMainSite = $isDevEnv && gw_deploy_http_conf_dev_ssl_enabled() && ($site['server_name'] ?? '') === $mainHost . '.localhost';

		if (!$isMainSite && !$isDevMainSite)
			continue;

		$domains = [$mainHost];
		foreach ((array)($site['ssl_aliases'] ?? []) as $alias) {
			if (strpos($alias, '*') !== false || substr($alias, -10) === '.localhost')
				continue;

			if (strpos($alias, '.') === false || gw_deploy_http_conf_is_xweb_alias($alias))
				continue;

			gw_deploy_http_conf_add_host($domains, $alias);
		}
		
		$out[] = [
			'name' => $mainCertName,
			'domains' => $domains,
			'manual_dns' => true,
			'cert_file' => gw_deploy_http_conf_cert_file($mainCertName, 'cert.pem'),
			'key_file' => gw_deploy_http_conf_cert_file($mainCertName, 'privkey.pem'),
		];
	}
	
	return $out;
}

function gw_deploy_build_http_conf()
{
	$sites = gw_deploy_http_conf_sites();
	$out = "# This file is generated from deploy/http_conf.php. Do not edit generated copies by hand.\n";
	foreach (gw_deploy_http_conf_certbot_configs() as $cert) {

		foreach ($cert['domains'] as $idx => $domain)
			$out .= "#  -d '" . $domain . "'" . ($idx === count($cert['domains']) - 1 ? "\n" : " \\\n");
		$out .= "#\n";
	}
	$out .= "# apt install python3-certbot-dns-cloudflare\n\n";
	
	foreach ($sites as $site) {
		foreach (($site['ports'] ?? [443, 80]) as $port)
			$out .= gw_deploy_http_conf_vhost($site, $port) . "\n";
	}
	
	return rtrim($out) . "\n";
}

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
	if (!class_exists('GW')) {
		chdir(__DIR__ . '/../');
		include_once __DIR__ . '/../init_basic.php';
	}
	
	echo gw_deploy_build_http_conf();
}
