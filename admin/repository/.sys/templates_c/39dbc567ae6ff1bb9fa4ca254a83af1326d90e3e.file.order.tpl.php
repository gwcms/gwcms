<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:35
         compiled from "/var/www/gw_cms/admin/templates/list/order.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13915849525056a7e38e3925-02397088%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39dbc567ae6ff1bb9fa4ca254a83af1326d90e3e' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/order.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13915849525056a7e38e3925-02397088',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_php')) include '/var/www/gw_cms/admin/lib/smarty/plugins/block.php.php';
?><?php if (!$_smarty_tpl->getVariable('title')->value){?>
	<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable(FH::fieldTitle($_smarty_tpl->getVariable('name')->value), null, null);?>
<?php }?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; smarty_block_php(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	$vars = FH::getTplVars($template,Array('m','name'));
	$order = $vars['m']->list_params['order'];
	
	$variants1=Array('desc','asc');
	
	foreach(explode(',', $vars['name']) as $name)
	{
		$variants[0].=($variants[0]?',':'')."$name ASC";
		$variants[1].=($variants[1]?',':'')."$name DESC";
	}

	$param = $variants[$tmp = intval(strpos($order, 'DESC')===false)];
	$curr_dir = $variants1[$tmp];
	
	$template->assign('order', Array
	(
		'uri'=> Navigator::buildURI(false, Array('list_params' => Array('order'=>$param) ) ),
		'current'=>in_array($order, $variants) ? $curr_dir : false
	));
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_php(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<?php if ($_smarty_tpl->getVariable('order')->value['current']){?><img style="padding: 2px" src="img/icons/order_<?php echo $_smarty_tpl->getVariable('order')->value['current'];?>
.png" align="absmiddle" onclick="$(this).next().click()" /><?php }?>
<a href="<?php echo $_smarty_tpl->getVariable('order')->value['uri'];?>
" <?php if ($_smarty_tpl->getVariable('order')->value['current']){?>style="font-weight:bold"<?php }?>><?php echo $_smarty_tpl->getVariable('title')->value;?>
</a>