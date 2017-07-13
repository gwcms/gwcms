<?php

GW::s('SW_NOTIFICATIONS', false);
GW::s('MASTER_MAIL', 'info@gw.lt');
GW::s('SYS_MAIL', GW::s('MASTER_MAIL'));
GW::s('DB/UPHD', 'root:ino@localhost/gw_cms');
GW::s('PROJECT_NAME', 'gwcms');
GW::s("SITE_URL",'http://localhost/gw/gwcms/');

GW::s('DEFAULT_APPLICATION','ADMIN');

GW::s('SITE_TITLE', 'GW CMS '.GW::s('GW_CMS_VERSION'));
GW::s('SITE_TITLE_DETAIL', 'GateWay Content Management System. v'.GW::s('GW_CMS_VERSION'));