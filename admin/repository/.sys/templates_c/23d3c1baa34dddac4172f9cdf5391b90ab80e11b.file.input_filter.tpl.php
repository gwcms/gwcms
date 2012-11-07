<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:19:42
         compiled from "/var/www/gw_cms/admin/templates/elements/input_filter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14821314905056a7e37fd4a2-49998093%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '23d3c1baa34dddac4172f9cdf5391b90ab80e11b' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/input_filter.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14821314905056a7e37fd4a2-49998093',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/gw_cms/admin/lib/smarty/plugins/modifier.escape.php';
if (!is_callable('smarty_function_html_options')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.html_options.php';
?><?php if (!$_smarty_tpl->getVariable('input_name_pattern')->value){?>
	<?php $_smarty_tpl->tpl_vars['input_name_pattern'] = new Smarty_variable("filters[%s][]", null, null);?>
<?php }?>


<?php  $_smarty_tpl->tpl_vars['param'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['param']->key => $_smarty_tpl->tpl_vars['param']->value){
?>
	<?php $_smarty_tpl->tpl_vars[$_smarty_tpl->tpl_vars['param']->key] = new Smarty_variable($_smarty_tpl->tpl_vars['param']->value, null, null);?>
<?php }} ?>

<?php $_smarty_tpl->tpl_vars['input_name'] = new Smarty_variable(sprintf($_smarty_tpl->getVariable('input_name_pattern')->value,$_smarty_tpl->getVariable('name')->value), null, null);?>

<?php $_smarty_tpl->tpl_vars['data'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('m')->value->list_params['filters'][$_smarty_tpl->getVariable('name')->value])===null||$tmp==='' ? array() : $tmp), null, null);?>

<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('title')->value)===null||$tmp==='' ? FH::fieldTitle($_smarty_tpl->getVariable('name')->value) : $tmp), null, null);?>
<?php $_smarty_tpl->tpl_vars['value'] = new Smarty_variable($_smarty_tpl->getVariable('data')->value[1], null, null);?>
<?php $_smarty_tpl->tpl_vars['filter_type'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('data')->value[0])===null||$tmp==='' ? $_smarty_tpl->getVariable('filter_type')->value : $tmp), null, null);?>


<?php $_smarty_tpl->tpl_vars['inp_type'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('type')->value)===null||$tmp==='' ? 'text' : $tmp), null, null);?>

<tr>
	<td><?php echo $_smarty_tpl->getVariable('title')->value;?>
</td>
	<td>
		<?php if (strpos($_smarty_tpl->getVariable('type')->value,'select')!==false){?>
			<input type="hidden" name="<?php echo $_smarty_tpl->getVariable('input_name')->value;?>
" value="<?php if ($_smarty_tpl->getVariable('type')->value=="multiselect"){?>IN<?php }else{ ?>=<?php }?>" />
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['compare_opt'] = new Smarty_variable(array('LIKE'=>'~','='=>'=','<'=>'<','>'=>'>','!='=>'&ne;'), null, null);?>
			<?php echo smarty_function_html_options(array('name'=>$_smarty_tpl->getVariable('input_name')->value,'options'=>$_smarty_tpl->getVariable('compare_opt')->value,'selected'=>(($tmp = @$_smarty_tpl->getVariable('filter_type')->value)===null||$tmp==='' ? 'LIKE' : $tmp)),$_smarty_tpl);?>

		<?php }?>
	</td>
	<td nowrap>
		<?php if ($_smarty_tpl->getVariable('type')->value=='multiselect'){?>
			<?php $_smarty_tpl->tpl_vars['input_name_pattern'] = new Smarty_variable(($_smarty_tpl->getVariable('input_name_pattern')->value)."[]", null, null);?>
			<?php $_smarty_tpl->tpl_vars['selected'] = new Smarty_variable(array_splice($_smarty_tpl->getVariable('data')->value,1), null, null);?>
		<?php }elseif($_smarty_tpl->getVariable('type')->value=='select'){?>

			<?php $_smarty_tpl->tpl_vars['options'] = new Smarty_variable($_smarty_tpl->getVariable('lang')->value['FILTER_EMPTY_OPTION']+(($tmp = @$_smarty_tpl->getVariable('options')->value)===null||$tmp==='' ? array() : $tmp), null, null);?>
		<?php }?>
		
		
		<?php $_template = new Smarty_Internal_Template_Custom("elements/inputs/".($_smarty_tpl->getVariable('inp_type')->value).".tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>    
	</td>
</tr>