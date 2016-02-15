

#sukurti patch su git per komandine eilute // VERSIJOS ID GALIM RAST PER git log
git diff VERSIJOSID HEAD > /tmp/test.diff

#ikelti patch
cd /var/www/gw/project && patch  -p1 < /tmp/test.diff

#repositorija padaryt readwrite for all
chmod -R a+rwX repository