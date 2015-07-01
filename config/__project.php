<?php


GW::s('MASTER_MAIL', 'info@gw.lt');
GW::s('SYS_MAIL', GW::s('MASTER_MAIL'));
GW::s('DB/UPHD', 'user:pass@localhost/databasename');
GW::s('PROJECT_NAME', 'Projekto pavadinimas');
GW::s("SITE_URL",'http://localhost/path/to/project/');

GW::s('DEFAULT_APPLICATION','ADMIN');

GW::s('SITE_TITLE', 'GW CMS '.GW::s('SYS_VERSION'));
GW::s('SITE_TITLE_DETAIL', 'GateWay Content Management System. v'.GW::s('SYS_VERSION'));