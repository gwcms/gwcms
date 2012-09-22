<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/submenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:239581925056a7e94321c9-91733750%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8c20bd38485ec4c30c0f48cd6f90984e0cd2202f' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/submenu.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '239581925056a7e94321c9-91733750',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_smarty_tpl->tpl_vars['first_pageid'] = new Smarty_variable($_smarty_tpl->getVariable('request')->value->path_arr[0], null, null);?>

<ul id="sub-nav">
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('request')->value->sitemap->map[$_smarty_tpl->getVariable('first_pageid')->value]['childs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
		<li <?php if ($_smarty_tpl->getVariable('request')->value->path==$_smarty_tpl->tpl_vars['item']->value['path']){?>class="current"<?php }?>>
			<a href="<?php echo $_smarty_tpl->getVariable('request')->value->ln;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['path'];?>
"><span><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</span></a>
		</li>
	<?php }} ?>
</ul>