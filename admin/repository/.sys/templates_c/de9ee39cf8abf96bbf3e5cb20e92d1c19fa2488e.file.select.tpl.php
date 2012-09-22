<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/elements/inputs/select.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18484458135056a7e96b6ea2-20702949%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de9ee39cf8abf96bbf3e5cb20e92d1c19fa2488e' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/inputs/select.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18484458135056a7e96b6ea2-20702949',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_html_options')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.html_options.php';
?><?php if ($_smarty_tpl->getVariable('empty_option')->value){?>
	<?php $_smarty_tpl->tpl_vars['options'] = new Smarty_variable($_smarty_tpl->getVariable('lang')->value['EMPTY_OPTION']+$_smarty_tpl->getVariable('options')->value, null, null);?>
<?php }?>

<?php echo smarty_function_html_options(array('name'=>$_smarty_tpl->getVariable('input_name')->value,'selected'=>$_smarty_tpl->getVariable('value')->value,'options'=>$_smarty_tpl->getVariable('options')->value,'onchange'=>$_smarty_tpl->getVariable('onchange')->value),$_smarty_tpl);?>

