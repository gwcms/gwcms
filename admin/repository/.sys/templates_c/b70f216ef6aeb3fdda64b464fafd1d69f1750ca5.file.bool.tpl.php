<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/elements/inputs/bool.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3060658195056a7e96f4b13-63364686%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b70f216ef6aeb3fdda64b464fafd1d69f1750ca5' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/inputs/bool.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3060658195056a7e96f4b13-63364686',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/gw_cms/admin/lib/smarty/plugins/modifier.escape.php';
?><input <?php if ($_smarty_tpl->getVariable('hidden_note')->value){?>title="<?php echo $_smarty_tpl->getVariable('hidden_note')->value;?>
"<?php }?> type="checkbox" <?php if ($_smarty_tpl->getVariable('value')->value){?>CHECKED<?php }?> onclick="$(this).next().val(this.checked ? 1 : 0)" />
<input type="hidden" name="<?php echo $_smarty_tpl->getVariable('input_name')->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('value')->value);?>
" />
