<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:35
         compiled from "/var/www/gw_cms/admin/templates/list/filters.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21049196515056a7e37bc866-50260747%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a5e1af1a09b0361cd3070c272eea1e5e6aad8064' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/filters.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21049196515056a7e37bc866-50260747',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div id="filters" <?php if (!$_smarty_tpl->getVariable('m')->value->list_params['filters']){?>style="display:none"<?php }?>>


<form method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>
">
	<input type="hidden" name="filters_unset" value="0">
	<input type="hidden" name="act" value="do:set_filters">

	<table>
		<tr>
			<td>
	
	<table class="gwTable" cellspacing="" cellpadding="1">
		<tr>
			<th><?php echo $_smarty_tpl->getVariable('lang')->value['FIELD'];?>
</th>
			<th title="<?php echo $_smarty_tpl->getVariable('lang')->value['COMPARE_TYPE']['FULL'];?>
"><?php echo $_smarty_tpl->getVariable('lang')->value['COMPARE_TYPE']['SHORT'];?>
</th>
			<th><?php echo $_smarty_tpl->getVariable('lang')->value['FILTER_VALUE'];?>
</th>
		</tr>
	

		
		<?php  $_smarty_tpl->tpl_vars['filter'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dl_filters')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['filter']->key => $_smarty_tpl->tpl_vars['filter']->value){
?>
			<?php if ($_smarty_tpl->tpl_vars['filter']->value){?>
				<?php $_template = new Smarty_Internal_Template_Custom("elements/input_filter.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name',$_smarty_tpl->tpl_vars['filter']->key);$_template->assign('params',$_smarty_tpl->tpl_vars['filter']->value); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
			<?php }?>
		<?php }} ?>
		
	</table>
	
	</td><td valign="top">
	
		<button ><?php echo $_smarty_tpl->getVariable('lang')->value['APPLY_FILTER'];?>
</button><br>
		<button style="margin-top:5px" onclick="this.form.elements['filters_unset'].value=1;"><?php echo $_smarty_tpl->getVariable('lang')->value['REMOVE_FILTER'];?>
</button>
	
	</td></tr>
	
	</table>
	</form>

</div>