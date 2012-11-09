<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:56
         compiled from "/var/www/gw_cms/admin/templates/elements/input.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3243131605056a825a517c9-62376720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d981e8aa5236ce5f56f449ebc88608266a26171' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/input.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3243131605056a825a517c9-62376720',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<?php if (!isset($_REQUEST['lang'])||GW::$request->ln==$_REQUEST['lang']||$_smarty_tpl->getVariable('i18n')->value||$_smarty_tpl->getVariable('item')->value->isI18NField($_smarty_tpl->getVariable('name')->value)){?>


<?php if (!$_smarty_tpl->getVariable('input_name_pattern')->value){?>
	<?php $_smarty_tpl->tpl_vars['input_name_pattern'] = new Smarty_variable("item[%s]", null, null);?>
	<?php if ($_smarty_tpl->getVariable('type')->value=='multiselect'){?><?php $_smarty_tpl->tpl_vars['input_name_pattern'] = new Smarty_variable(($_smarty_tpl->getVariable('input_name_pattern')->value)."[]", null, null);?><?php }?>
<?php }?>
<?php $_smarty_tpl->tpl_vars['input_name'] = new Smarty_variable(sprintf($_smarty_tpl->getVariable('input_name_pattern')->value,$_smarty_tpl->getVariable('name')->value), null, null);?>
<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('title')->value)===null||$tmp==='' ? FH::fieldTitle($_smarty_tpl->getVariable('name')->value) : $tmp), null, null);?>


<?php if (!$_smarty_tpl->getVariable('value')->value){?>
	<?php $_smarty_tpl->tpl_vars['value'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->get($_smarty_tpl->getVariable('name')->value), null, null);?>
		
	<?php if ($_smarty_tpl->getVariable('data_type')->value=='numeric'&&!$_smarty_tpl->getVariable('value')->value){?>
		<?php $_smarty_tpl->tpl_vars['value'] = new Smarty_variable($_smarty_tpl->getVariable('default')->value, null, null);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['value'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('value')->value)===null||$tmp==='' ? $_smarty_tpl->getVariable('default')->value : $tmp), null, null);?>
	<?php }?>
	
<?php }?>

<tr id="gw_input_<?php echo $_smarty_tpl->getVariable('name')->value;?>
">
	<td class="input_label_td" width="<?php echo $_smarty_tpl->getVariable('width_title')->value;?>
" <?php if ($_smarty_tpl->getVariable('m')->value->error_fields[$_smarty_tpl->getVariable('name')->value]){?>class="error_label"<?php }?><?php if ($_smarty_tpl->getVariable('nowrap')->value){?> nowrap<?php }?>>
		<span style="white-space:nowrap;">
			<?php if ($_smarty_tpl->getVariable('hidden_note')->value){?><span class="tooltip" title="<?php echo $_smarty_tpl->getVariable('hidden_note')->value;?>
"><?php echo $_smarty_tpl->getVariable('title')->value;?>
</span><?php }else{ ?><?php echo $_smarty_tpl->getVariable('title')->value;?>
<?php }?>
			<?php if ($_smarty_tpl->getVariable('i18n')->value||$_smarty_tpl->getVariable('item')->value->i18n_fields[$_smarty_tpl->getVariable('name')->value]){?><sup title="International" class="i18n_tag">(Int)</sup><?php }?>
		</span>

		<?php if ($_smarty_tpl->getVariable('note')->value){?><br /><small class="input_note"><?php echo $_smarty_tpl->getVariable('note')->value;?>
</small><?php }?>	
		
	</td>
	<td class="input_td" width="<?php echo $_smarty_tpl->getVariable('width_input')->value;?>
">
	<?php if ($_smarty_tpl->getVariable('did_note')->value){?><small><?php echo $_smarty_tpl->getVariable('did_note')->value;?>
</small><?php }?>  
	<?php $_smarty_tpl->tpl_vars['inp_type'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('type')->value)===null||$tmp==='' ? 'text' : $tmp), null, null);?>
	
	<?php if ($_smarty_tpl->getVariable('type')->value=='password'){?><?php $_smarty_tpl->tpl_vars['inp_type'] = new Smarty_variable('text', null, null);?><?php }?>
	<?php $_template = new Smarty_Internal_Template_Custom("elements/inputs/".($_smarty_tpl->getVariable('inp_type')->value).".tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>  
	
	</td>
	
</tr>

<?php }?>