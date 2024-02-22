

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



play_playoff_algo =>  play_offs_algo
play_number_of_groups => play_rr_number_of_groups
play_teams_per_group => play_rr_teams_per_group
play_number_of_teams_exits => play_offs_team_count
play_fight_positions =>play_offs_fight_positions
play_limit_lose => play_offs_limit_lose
play_match_point => play_offs_match_point


php7.4 /var/www/gw/gwcms/composer.phar require yetanotherape/diff-match-patch