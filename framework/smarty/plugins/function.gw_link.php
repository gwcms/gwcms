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
function smarty_function_gw_link($params, &$smarty) {

		//$vars = FH::getTplVars($smarty, Array('app'));
		//FH::gw_link($params, $vars['app']);
		$app = $smarty->getVariable('app')->value;

		return $app->fh()->gw_link($params);
}

/* vim: set expandtab: */
?>
