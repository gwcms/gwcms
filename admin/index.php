<?

$debug=Array('mem_use'=>Array(memory_get_usage(true)));

include 'compatability.php';
include 'init.php';
include 'init_auth.php';


//$_SESSION['cms_auth']['user_id']=1;

$GLOBALS['proc_timer']=new GW_Timer();

GW_ADM_Sitemap_Helper::updateSitemap();

GW::$request=new GW_Request();
GW::$request->init();

GW_Error_Message::$ln = GW::$request->ln;
GW::$lang=GW_Lang_XML::load(GW::$dir['ADMIN']."lang/lang.xml", GW::$request->ln);	
include GW::$dir['ADMIN'].'init_smarty.php';

GW::$request->process();


GW_Debug_Helper::show_debug_info();


echo "<!--process time ".$GLOBALS['proc_timer']->stop(5)."-->";
