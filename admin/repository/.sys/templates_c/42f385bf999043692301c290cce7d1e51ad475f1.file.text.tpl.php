<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:19:42
         compiled from "/var/www/gw_cms/admin/templates/elements/inputs/text.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18892813455056a7e3894df0-68329476%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '42f385bf999043692301c290cce7d1e51ad475f1' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/inputs/text.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18892813455056a7e3894df0-68329476',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/gw_cms/admin/lib/smarty/plugins/modifier.escape.php';
?>
<input name="<?php echo $_smarty_tpl->getVariable('input_name')->value;?>
" type="<?php echo $_smarty_tpl->getVariable('type')->value;?>
" value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('value')->value);?>
" onchange="this.value=$.trim(this.value);" <?php if ($_smarty_tpl->getVariable('readonly')->value){?>readonly<?php }?>
<?php if ($_smarty_tpl->getVariable('maxlength')->value){?>maxlength="<?php echo $_smarty_tpl->getVariable('maxlength')->value;?>
"<?php }?> style="width: <?php echo (($tmp = @$_smarty_tpl->getVariable('width')->value)===null||$tmp==='' ? "100%" : $tmp);?>
;" <?php if ($_smarty_tpl->getVariable('hidden_note')->value){?>title="<?php echo $_smarty_tpl->getVariable('hidden_note')->value;?>
"<?php }?> />
