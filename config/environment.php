<?php

define('GW_ENV_DEV',1);
define('GW_ENV_TEST',2);
define('GW_ENV_PROD',3);



GW::s('PROJECT_ENVIRONMENT', GW_ENV_DEV);
