<?php
// CODE IS UNDER VERSION CONTROL
// git@github.com:gwcms/gwcms.git
// UNDER FOLDER zzz_setup
// FILENAME create_project.php
// 
//fast project intit script


$projname=readline("Enter project name: ");
echo "projectname=$projname\n";




if( readline("Init git dir? [y/N]: ")=='y'){
	$git_project_dir=__DIR__.'/'.$projname;

	if(is_dir($git_project_dir))
	{
		echo "Error $git_project_dir is already existing directory\n";
		exit;
	}	
	
	mkdir($git_project_dir);
	shell_exec("cd '$git_project_dir' &&  git --bare init");
	shell_exec("chown git:git -R '$git_project_dir'");

	echo "Repos ready:\nssh://git@serv2.voro.lt{$git_project_dir}\n";
}
      
        
if( readline("Init apache cfg? [y/N]: ")=='y'){

	$domain=readline("Enter site domain: ");

$cfg="
<VirtualHost *:80>
	ServerName $projname	
	ServerAlias $domain
	DocumentRoot '/var/www/$projname'

	<Directory /var/www/$projname>
		Options MultiViews FollowSymLinks
		AllowOverride All
	</Directory>
</VirtualHost>
<VirtualHost *:443>
	ServerName $projname	
	ServerAlias $domain
	DocumentRoot '/var/www/$projname'

	<Directory /var/www/$projname>
		Options MultiViews FollowSymLinks
		AllowOverride All
	</Directory>
	
	SSLCertificateFile /etc/letsencrypt/live/$domain/cert.pem
	SSLCertificateChainFile /etc/letsencrypt/live/$domain/chain.pem
	SSLCertificateKeyFile /etc/letsencrypt/live/$domain/privkey.pem
	
	SSLEngine On
</VirtualHost>
";
	if(is_dir("/var/www/$projname"))
	{
		echo "Error /var/www/$projname is already existing directory\n";
		exit;
	}
	mkdir("/var/www/$projname");
	passthru("chown git:git -R '/var/www/$projname'");
	
	file_put_contents('/etc/apache2/sites-enabled/'.$projname.'.conf', $cfg);
	
	if(isset($git_project_dir)){
		$cfg =  "#!/bin/sh
GIT_WORK_TREE=/var/www/$projname git checkout -f && git rev-parse HEAD > /var/www/$projname/version  && php /var/www/$projname/applications/cli/after_deploy.php
";
		file_put_contents($hookf=$git_project_dir."/hooks/post-receive", $cfg);
		file_put_contents('/etc/hosts', "\n127.0.0.1  $projectname\n" ,FILE_APPEND);
		
		passthru("chmod +x '$hookf'");
		
		echo "PERFORM SSL setup $domain\n";
		
		passthru("service apache2 stop");
		
		passthru("letsencrypt certonly --standalone -d $domain");
		
		passthru("service apache2 restart");
		echo "PERFORM SSL setup $domain\n";
	}
}


/*
CREATE USER 'natosnew.menuturas.lt'@'localhost' IDENTIFIED VIA mysql_native_password USING '***';
CREATE DATABASE IF NOT EXISTS `natosnew.menuturas.lt`;
GRANT ALL PRIVILEGES ON `natosnew.menuturas.lt`.* TO 'natosnew.menuturas.lt'@'localhost';
*/


