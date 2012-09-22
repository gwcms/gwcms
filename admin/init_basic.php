<?

include_once __DIR__.'/lib/gw.class.php';
include_once __DIR__.'/config/main.php';

include GW::$dir['LIB'].'sys_common.php';

GW_Autoload::init();

ini_set("include_path",  GW::$dir['PEAR'].':'.ini_get('include_path'));

include __DIR__.'/init_errors.php';

GW::$db = new GW_DB();

if(isset($_SESSION['debug']))
	GW::$db->debug=true;