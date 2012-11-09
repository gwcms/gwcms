<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:01
         compiled from "/var/www/gw_cms/admin/templates/menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20513426575056a7e9464317-61303590%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'daa6800c06674c0ffcb75a79577911b9fa125bcc' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/menu.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20513426575056a7e9464317-61303590',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div id="firstpane" class="menu_list"> <!--Code for menu starts here-->



<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = GW::getInstance('GW_ADM_Page')->getChilds(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>

	<?php $_smarty_tpl->tpl_vars['active'] = new Smarty_variable(($_smarty_tpl->getVariable('request')->value->path_arr[0]['path_clean']==$_smarty_tpl->getVariable('item')->value->pathname), null, null);?>
	<?php $_smarty_tpl->tpl_vars['childs'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->getChilds(), null, null);?>

	<p class="menu_head<?php if ($_smarty_tpl->getVariable('active')->value){?> menu_head_active<?php }?> <?php if (!count($_smarty_tpl->getVariable('childs')->value)){?>no_childs<?php }?>">
		<a href="<?php echo $_smarty_tpl->getVariable('request')->value->buildUri($_smarty_tpl->getVariable('item')->value->path);?>
" ><?php echo $_smarty_tpl->getVariable('item')->value->get('title',$_smarty_tpl->getVariable('ln')->value);?>
</a>
	</p>

	<?php if (count($_smarty_tpl->getVariable('childs')->value)){?>
	<div class="menu_body <?php if ($_smarty_tpl->getVariable('active')->value){?> menu_body_active<?php }?>">
			<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('childs')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
					<a <?php if ($_smarty_tpl->getVariable('request')->value->path_arr[1]['path_clean']==$_smarty_tpl->getVariable('item')->value->path){?>class="current"<?php }?> href="<?php echo $_smarty_tpl->getVariable('request')->value->buildUri($_smarty_tpl->getVariable('item')->value->path);?>
"><?php echo $_smarty_tpl->getVariable('item')->value->get('title',$_smarty_tpl->getVariable('ln')->value);?>
</a>
			<?php }} ?>
	</div>
	<?php }?>

<?php }} ?>

</div>

