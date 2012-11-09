<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:05
         compiled from "/var/www/gw_cms/admin/templates/breadcrumbs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4322732165056a7e94dec64-40061962%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30b19fc9151db3227c62488b62d3dbd9bc26f772' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/breadcrumbs.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4322732165056a7e94dec64-40061962',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<?php $_smarty_tpl->tpl_vars['list'] = new Smarty_variable($_smarty_tpl->getVariable('request')->value->path_arr, null, null);?>
<?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
 $_smarty_tpl->tpl_vars['i']->value = $_smarty_tpl->tpl_vars['val']->key;
?>
	<?php if (!isset($_smarty_tpl->tpl_vars['list']) || !is_array($_smarty_tpl->tpl_vars['list']->value)) $_smarty_tpl->createLocalArrayVariable('list', null, null);
$_smarty_tpl->tpl_vars['list']->value[$_smarty_tpl->tpl_vars['i']->value]['noln'] = 1;?>
<?php }} ?>

<?php if (is_array($_smarty_tpl->getVariable('breadcrumbs_attach')->value)){?>
	<?php $_smarty_tpl->tpl_vars['list'] = new Smarty_variable(array_merge($_smarty_tpl->getVariable('list')->value,$_smarty_tpl->getVariable('breadcrumbs_attach')->value), null, null);?>
<?php }?>

<?php if (count($_smarty_tpl->getVariable('list')->value)){?>
	<div id="breadcrumbs">
	
	<?php  $_smarty_tpl->tpl_vars['path'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['path']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['path']->iteration=0;
if ($_smarty_tpl->tpl_vars['path']->total > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['path']->key => $_smarty_tpl->tpl_vars['path']->value){
 $_smarty_tpl->tpl_vars['path']->iteration++;
 $_smarty_tpl->tpl_vars['path']->last = $_smarty_tpl->tpl_vars['path']->iteration === $_smarty_tpl->tpl_vars['path']->total;
?>
	
		<?php if ($_smarty_tpl->tpl_vars['path']->value['title']){?>
			<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['path']->value['title'], null, null);?>
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['item'] = new Smarty_variable(GW::getInstance('GW_ADM_Page')->getByPath($_smarty_tpl->tpl_vars['path']->value['path']), null, null);?>
			<?php if ($_smarty_tpl->getVariable('item')->value){?>
				<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->title, null, null);?>
				
				<?php $_smarty_tpl->tpl_vars['do'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->getDataObject(), null, null);?>
				<?php if ($_smarty_tpl->getVariable('do')->value){?>
					<?php $_smarty_tpl->tpl_vars['dot'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('do')->value->title)===null||$tmp==='' ? $_smarty_tpl->getVariable('item')->value->data_object_id : $tmp), null, null);?>
				<?php }?>

				<?php if ($_smarty_tpl->getVariable('dot')->value){?>
					<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable(($_smarty_tpl->getVariable('title')->value)." (".($_smarty_tpl->getVariable('dot')->value).")", null, null);?>
				<?php }?>
				
			<?php }else{ ?>
				<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable(FH::viewTitle($_smarty_tpl->tpl_vars['path']->value['name']), null, null);?>
			<?php }?>
		<?php }?>
		
		<?php if (!$_smarty_tpl->getVariable('title')->value){?>
			<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->tpl_vars['path']->value['name'], null, null);?>
		<?php }?>
	
			
		<?php if ($_smarty_tpl->tpl_vars['path']->last){?>
			<?php echo $_smarty_tpl->getVariable('title')->value;?>

		<?php }else{ ?>
			<a href="<?php if ($_smarty_tpl->tpl_vars['path']->value['noln']){?><?php echo $_smarty_tpl->getVariable('ln')->value;?>
/<?php }?><?php echo $_smarty_tpl->tpl_vars['path']->value['path'];?>
"><?php echo $_smarty_tpl->getVariable('title')->value;?>
</a> &raquo;
		<?php }?>	
	
	<?php }} ?>
	</div>
<?php }?>

