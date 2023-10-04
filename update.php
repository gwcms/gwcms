<?php


include "init_basic.php";
GW::s('MODULE_OR_CORE_DIR', __DIR__);
include __DIR__.'/framework/cms/gw_cms_sync.class.php';
include __DIR__.'/framework/cms/gw_cms_sync_update.php';


/*

echo "Enter command\n";
while (false !== ($line = fgets(STDIN))) {
	processCommand($line);
	echo "Done, enter next command\n";
}
 * */