

#sukurti patch su git per komandine eilute // VERSIJOS ID GALIM RAST PER git log
git diff VERSIJOSID HEAD > /tmp/test.diff

#ikelti patch
cd /var/www/gw/project && patch  -p1 < /tmp/test.diff

#repositorija padaryt readwrite for all
chmod -R a+rwX repository

//whereis php
export VISUAL=nano; crontab -e
*/5 * * * * /usr/local/bin/php ~/www/daemon/system.php -croncheck 2>&1 >> ~/www/repository/.sys/logs/system.log


usermod -aG sudo www-data

TODO:
 2016-08-29 add small documentation
 2016-08-29 add info about code writing agreements



php7.4 /var/www/gw/gwcms/composer.phar require yetanotherape/diff-match-patch



apt install php8.4-mysqli
apt install php8.4-soap
apt-get install php8.4-fpm
apt-get install php8.4-xml
apt-get install php8.4-intl
apt-get install php8.4-gd
apt-get install php8.4-curl
apt-get install php8.4-mcrypt
apt install php8.4-zip
apt install php8.4-mbstring



sftp://root@2.voro.lt/etc/php/8.4/fpm/pool.d/robotikosstudija.conf

[robotikosstudija]
user = www-data
group = www-data

listen = /run/php/php8.4-robotikosstudija.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 10
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

chdir = /var/www/mano.robotikosstudija.lt

;php_admin_value[disable_functions] = exec,shell_exec,system,passthru,popen,proc_open,eval,phpinfo,show_source
php_admin_value[open_basedir] = /var/www/mano.robotikosstudija.lt:/tmp:/var/www/common/environment
php_admin_value[upload_tmp_dir] = /tmp
php_admin_value[session.save_path] = /tmp
