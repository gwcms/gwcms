<?php

//add to /etc/sudoers line:
//www-data ALL = (root) NOPASSWD: /usr/bin/php /var/www/sms/revert_version.php LAST_STABLE, /usr/bin/php /var/www/sms/revert_version.php HEAD

if(isset($argv)){
    //parse shell script arguments into $_GET
    //php -f somefile.php a=1 b[]=2 b[]=3
    //parse_str(implode('&', array_slice($argv, 1)), $_GET);
}

$LAST_STABLE_VERSION = "3d65ed2d39c5652eccc6c2257faef63bf1338564";


if(isset($_SERVER['REMOTE_ADDR'])){
	header('Content-type: text/plain');
		
	echo "exec res:\n";
	
	
	$ARGS = isset($_GET['HEAD']) ? 'HEAD':'LAST_STABLE';
	$res=shell_exec($cmd="sudo /usr/bin/php ".__FILE__.' '.$ARGS.' 2>&1');

	echo "$cmd\n";
	echo "$res\n";
	echo ".";
	exit;
}


if(!isset($_SERVER['HTTP_HOST'])){


	if($argv[1]=='HEAD')
	{
		echo shell_exec("cd /home/git/bulksms.git/ && GIT_WORK_TREE=/var/www/sms/ git checkout -f && git rev-parse HEAD > /var/www/sms/version");
		echo "Now it should be HEAD version\n";
	}else{
		echo shell_exec("rm -rf /tmp/bulksms ; cd /tmp/ && git clone /home/git/bulksms.git && cd /tmp/bulksms &&  git checkout $LAST_STABLE_VERSION && GIT_WORK_TREE=/var/www/sms/ git checkout -f");
		file_put_contents("/var/www/sms/version", $LAST_STABLE_VERSION);
		
		echo "Now it should be LAST STABLE version\n";
	}
	
	//update version time
	shell_exec("cd /home/git/bulksms.git && git show -s --format=%ci `cat /var/www/sms/version` > /var/www/sms/versiontime");
	shell_exec("chown -R git:git /var/www/sms/ ; chmod -R a=rwX /var/www/sms/repository");
}