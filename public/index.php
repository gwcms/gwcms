<?php


$debug=Array('mem_use'=>Array(memory_get_usage(true)));


include_once __DIR__.'/init.php';


if(!is_array(GW::$static_conf['PUB_LANGS']))
die('no public');

include GW::$dir['MODULES'].'sitemap/gw_page.class.php';

$GLOBALS['proc_timer']=new GW_Timer();


GW::$request=new GW_Public_Request();


GW::$request->ifAjaxCallProcess();
GW::$request->init();


GW_Error_Message::$ln = GW::$request->ln;
GW_Error_Message::$langf_dir = GW::$dir['PUB_LANG'];

GW::$lang=GW_Lang_XML::load(GW::$dir['PUB']."lang/lang.xml", GW::$request->ln);



include GW::$dir['ADMIN'].'init_smarty.php';
include_once __DIR__.'/init_auth.php';

GW::$request->process();


echo "<script>dump('process_time=". $GLOBALS['proc_timer']->stop(5) ."');</script>";