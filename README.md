

#sukurti patch su git per komandine eilute // VERSIJOS ID GALIM RAST PER git log
git diff VERSIJOSID HEAD > /tmp/test.diff

#ikelti patch
cd /var/www/gwcms && patch  -p1 < /var/www/version_sync/gwcms_patches/1.diff

#repositorija padaryt readwrite for all
chmod -R a+rwX repository