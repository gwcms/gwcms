<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/tools/form_submit_buttons.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3938371545056a7e976a450-04700942%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bc2aa1168e991515d4a74d4d09e083b5967dbb9' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/tools/form_submit_buttons.tpl',
      1 => 1347674464,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3938371545056a7e976a450-04700942',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!$_smarty_tpl->getVariable('submit_buttons')->value){?>
	<?php $_smarty_tpl->tpl_vars['submit_buttons'] = new Smarty_variable(array('save','apply','cancel'), null, null);?>
<?php }?>

<div class="form_action_buttons">
	<input type="hidden" name="submit_type" value="0" />
	
	<?php if (in_array('save',$_smarty_tpl->getVariable('submit_buttons')->value)){?>
		<input onclick="remove_form_data_saver()" type="submit" value="<?php echo $_smarty_tpl->getVariable('lang')->value['SAVE'];?>
" /> 
	<?php }?>
	
	<?php if (in_array('apply',$_smarty_tpl->getVariable('submit_buttons')->value)){?>
		<input onclick="this.form.elements['submit_type'].value=1;remove_form_data_saver()" type="submit" value="<?php echo $_smarty_tpl->getVariable('lang')->value['APPLY'];?>
"/> 
	<?php }?>
	
	<?php if (in_array('cancel',$_smarty_tpl->getVariable('submit_buttons')->value)){?>
		<input onclick="history.go(-1);return false" type="submit" value="<?php echo $_smarty_tpl->getVariable('lang')->value['CANCEL'];?>
" />
	<?php }?>
</div>