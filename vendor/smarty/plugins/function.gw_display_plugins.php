<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */



function __smarty_function_gw_display_plugins($app)
{	
	$dir=GW::s("DIR/".$app->app_name."/TEMPLATES").'plugins/';
	
	$list = glob($dir.'*/*.tpl');
	
	$list1 = Array();
	
	foreach($list as $i => $item)
		$list[$i]=str_replace($dir, '', $item);
		
	//sort($list);
	
	foreach($list as $i => $item)
		$list1[dirname($item)][]=$item;
		
	return $list1;
}


/**
 * Smarty {gw_display_plugins} function plugin
 *
 * Type:     function<br>
 * Name:     gw<br>
 * Purpose:  plugins implementation<br>

 * @author vidmantas.norkus@gw.lt
 * @param array
 * @param Smarty
 */


function smarty_function_gw_display_plugins($params, &$smarty)
{
	static $list_plugins;
	
	if($list_plugins===Null)
		$list_plugins=__smarty_function_gw_display_plugins($smarty->getVariable('app')->value);
	
	
	foreach($list_plugins[$params['id']] as $tpl)
		$smarty->display('plugins/'.$tpl);
}

/* vim: set expandtab: */

?>
