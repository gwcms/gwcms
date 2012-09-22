<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/elements/inputs/htmlarea.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8801037325056a7e9715334-82886623%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bacee1e1f30ca09f93697cdc47f3e8fc6cac8771' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/elements/inputs/htmlarea.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8801037325056a7e9715334-82886623',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_php')) include '/var/www/gw_cms/admin/lib/smarty/plugins/block.php.php';
?>

<?php include_once ('ckeditor/ckeditor_php5.php');?>

<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; smarty_block_php(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	GW::$smarty->assign('ck', new CKEditor);
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_php(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php $_smarty_tpl->tpl_vars['width'] = new Smarty_variable((($tmp = @$_smarty_tpl->getVariable('width')->value)===null||$tmp==='' ? "800" : $tmp), null, null);?>
<?php echo $_smarty_tpl->getVariable('ck')->value->editor($_smarty_tpl->getVariable('input_name')->value,$_smarty_tpl->getVariable('value')->value,array('width'=>$_smarty_tpl->getVariable('width')->value,'language'=>GW::$request->ln));?>



