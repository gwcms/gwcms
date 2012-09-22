<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/modules/sitemap/tpl/pages/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14024098845056a7e9287df0-69702475%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0552c71a965be2fe1694e07c331e6088540a5b0e' => 
    array (
      0 => '/var/www/gw_cms/admin/modules/sitemap/tpl/pages/form.tpl',
      1 => 1347853796,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14024098845056a7e9287df0-69702475',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template_Custom("default_form_open.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>

<?php $_template = new Smarty_Internal_Template_Custom("tools/lang_select.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>


<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','type');$_template->assign('type','select');$_template->assign('options',$_smarty_tpl->getVariable('m')->value->lang['TYPE_OPT']);$_template->assign('onchange',"$"."('#gw_input_template_id')[this.value==0?'fadeIn':'fadeOut']().size()"); echo $_template->getRenderedTemplate();?><?php unset($_template);?>


<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','parent_id');$_template->assign('type','select');$_template->assign('options',$_smarty_tpl->getVariable('m')->value->getParentOpt($_smarty_tpl->getVariable('item')->value->id));$_template->assign('default',$_GET['pid']); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','pathname'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','title'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','meta_description'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>


<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name','template_id');$_template->assign('options',$_smarty_tpl->getVariable('lang')->value['EMPTY_OPTION']+$_smarty_tpl->getVariable('m')->value->getTemplateList());$_template->assign('type','select'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>


<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('type','bool');$_template->assign('name','active'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>

<?php if ($_smarty_tpl->getVariable('update')->value){?>
	<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('type','bool');$_template->assign('name','in_menu'); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	
	
	<?php $_smarty_tpl->tpl_vars['input_name_pattern'] = new Smarty_variable("item[input_data][%s]", null, null);?>
	<?php  $_smarty_tpl->tpl_vars['input'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('item')->value->getInputs(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['input']->key => $_smarty_tpl->tpl_vars['input']->value){
?>
	
		<?php $_template = new Smarty_Internal_Template_Custom("elements/input.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name',$_smarty_tpl->getVariable('input')->value->get('title'));$_template->assign('type',$_smarty_tpl->getVariable('input')->value->get('type'));$_template->assign('note',$_smarty_tpl->getVariable('input')->value->get('note'));$_template->assign('title',$_smarty_tpl->getVariable('input')->value->get('title'));$_template->assign('value',$_smarty_tpl->getVariable('item')->value->getContent($_smarty_tpl->getVariable('input')->value->get('title')));$_template->assign('i18n',true); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	
	<?php }} ?>
	
<?php }?>

<?php $_template = new Smarty_Internal_Template_Custom("default_form_close.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('extra_fields',array('id','path','insert_time','update_time')); echo $_template->getRenderedTemplate();?><?php unset($_template);?>