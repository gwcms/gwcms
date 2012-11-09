<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:56
         compiled from "/var/www/gw_cms/admin/templates/extra_info.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1494628315056a7e97a3c98-53724561%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4ad9355cf5e5f5375fb2f77bd803d80fbb3e4c1f' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/extra_info.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1494628315056a7e97a3c98-53724561',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!count($_smarty_tpl->getVariable('extra_fields')->value)){?>
	<?php $_smarty_tpl->tpl_vars['extra_fields'] = new Smarty_variable(array('id','insert_time','update_time'), null, null);?>
<?php }?>

	
<table class="gwTable">
	<th colspan="2" class="th_h3 th_single"><?php echo $_smarty_tpl->getVariable('lang')->value['EXTRA_INFO'];?>
</th>

<?php  $_smarty_tpl->tpl_vars['field_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('extra_fields')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['field_id']->key => $_smarty_tpl->tpl_vars['field_id']->value){
?>
	<tr>
		<td width="1%" nowrap><?php echo FH::fieldTitle($_smarty_tpl->tpl_vars['field_id']->value);?>
</td>
		<td width="99%">
			<?php $_smarty_tpl->tpl_vars['x'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->get($_smarty_tpl->tpl_vars['field_id']->value), null, null);?>
			<?php if (is_array($_smarty_tpl->getVariable('x')->value)){?>
				<?php echo dump($_smarty_tpl->getVariable('x')->value);?>

			<?php }else{ ?>
				<?php echo $_smarty_tpl->getVariable('x')->value;?>

			<?php }?>
		</td>
	</tr>		
<?php }} ?>
</table>