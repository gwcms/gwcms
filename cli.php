<?php

//run from console
//php -a -d auto_prepend_file=cli.php

include "init_basic.php";

$app = GW::initApp('admin',['path_arr' => [], 'app_base' => '', 'args' => [], 'sys_base' => '', 'ob_notstart'=>1]);


///$app->init();
//