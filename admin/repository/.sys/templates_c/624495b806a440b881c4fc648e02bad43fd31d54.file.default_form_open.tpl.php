<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/default_form_open.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13140176855056a7e93468e2-79378702%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '624495b806a440b881c4fc648e02bad43fd31d54' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_form_open.tpl',
      1 => 1347852734,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13140176855056a7e93468e2-79378702',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template_Custom("default_open.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>



<table style="width:600px">
<tr>
<td>

<?php $_smarty_tpl->tpl_vars["width_title"] = new Smarty_variable("30%", null, 2);?>


<form id="itemform" action="<?php echo $_SERVER['REQUEST_URI'];?>
" method="post"  enctype="multipart/form-data" >

<input type="hidden" name="act" value="do:<?php echo (($tmp = @$_smarty_tpl->getVariable('action')->value)===null||$tmp==='' ? "save" : $tmp);?>
" />
<input type="hidden" name="item[id]" value="<?php echo $_smarty_tpl->getVariable('item')->value->id;?>
" />

<table class="gwTable" style="width:100%">