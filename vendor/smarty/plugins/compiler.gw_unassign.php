<?php
/**
 * Smarty {unassign} compiler function plugin
 *
 * File:     compiler.gw_unassign.php<br>
 * Type:     compiler function<br>
 * Name:     gw_unassign<br>
 * Purpose:  gw_unassign a template variable or an item of an array.
 *
 * @link cms.gw.lt
 * @version 2.0
 * @copyright Copyright 2011 GW CMS
 * @author Vidmantas Norkus <vidmantas.norkus@gw.lt>
 *
 * @param string containing var-attribute
 * @param Smarty_Compiler object
 * @return void|string
 */

function smarty_compiler_gw_unassign($params, &$smarty) 
{
    if (!isSet($params['var'])) {
        $smarty->_syntax_error("unassign: missing 'var' parameter", E_USER_WARNING);
        return;
    }

    return "<?php unset({$params['var']});?>";
}

?>