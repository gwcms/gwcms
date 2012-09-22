<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/default_form_close.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5922599075056a7e9754643-06623771%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a848c3ecc77e508b763f7c74b34858e509e70077' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_form_close.tpl',
      1 => 1347849549,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5922599075056a7e9754643-06623771',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>



</table>
	<?php $_template = new Smarty_Internal_Template_Custom("tools/form_submit_buttons.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
</form>



<?php if ($_smarty_tpl->getVariable('update')->value){?>
	<?php $_template = new Smarty_Internal_Template_Custom("extra_info.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php }?>

</table>


<?php $_template = new Smarty_Internal_Template_Custom("default_close.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>