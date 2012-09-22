<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {gw_link} function plugin
 *
 * Type:     function<br>
 * Name:     gw<br>
 * Purpose:  simplify links<br>

 * @author vidmantas.norkus@gw.lt
 * @param array
 * @param Smarty
 */

function smarty_function_gw_path($params, &$smarty)
{
	return FH::gw_path($params);
}

/* vim: set expandtab: */

?>
