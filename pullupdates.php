<?php

echo date("Y-m-d H:i:s");
echo "<br/>";
//test
//this is for automatic updates, called from github after push to master branch
//if you would like get realtime updates email me vidmantas.norkus@gw.lt your http://project.com/pullupdates.php link

//add to /etc/sudoers line:
//www-data ALL = (root) NOPASSWD: /usr/bin/php /var/www/karolisvil/padangu-arsenalas.lt/public/pullupdates.php

if(isset($argv)){
    //parse shell script arguments into $_GET
    //php -f somefile.php a=1 b[]=2 b[]=3
    //parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

if(isset($_SERVER['REMOTE_ADDR'])){
	header('Content-type: text/plain');
		
	echo "exec res:\n";
	$res=shell_exec($cmd="sudo /usr/bin/php ".__FILE__.' 2>&1');
	//echo $cmd;
	
	echo $res;
	echo ".";
	
	exit;
}


if(!isset($_SERVER['HTTP_HOST'])){
	echo "usr {$_SERVER['USER']}. pulling\n";
	$dir = __DIR__;
	echo shell_exec("cd '$dir' && git pull 2>&1");
	echo shell_exec("cd '$dir' && rm repository/.sys/templates_c/*");
	echo shell_exec("cd '$dir' && git rev-parse --short HEAD > version");	
	
	include __DIR__.'/applications/cli/after_deploy.php';
}