<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19378579475056a7e93eea29-16972356%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd8a21e5610e678242dc26f988e0808985e7a4cf5' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/head.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19378579475056a7e93eea29-16972356',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_php')) include '/var/www/gw_cms/admin/lib/smarty/plugins/block.php.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; smarty_block_php(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	GW::$smarty->assign('session_exp', GW::$user ? GW::$user->remainingSessionTime() : -1);
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_php(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>



<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="<?php echo Navigator::getBase(1);?>
" />
	<title><?php echo (($tmp = @$_smarty_tpl->getVariable('title')->value)===null||$tmp==='' ? $_smarty_tpl->getVariable('request')->value->page->get('title',$_smarty_tpl->getVariable('ln')->value) : $tmp);?>
 - <?php echo $_smarty_tpl->getVariable('lang')->value['SITE_TITLE'];?>
</title>
	<meta name="description" content="<?php echo $_smarty_tpl->getVariable('lang')->value['GW_CMS_DESCRIPTION'];?>
" />
	<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="css/main.css" />
    <!--[if lte IE 1]><link rel="stylesheet" type="text/css" href="css/main_ie.css" /><![endif]-->
	
	<link type="text/css" href="css/jquery_ui/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
	
	<script type="text/javascript" src="js/jquery.min-latest.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.selectboxes.min.js"></script>	

	<script type="text/javascript" src="js/main.js"></script>
		
	
	<script type="text/javascript">
		$.extend(GW, { base:'<?php echo $_smarty_tpl->getVariable('request')->value->base;?>
', ln:'<?php echo $_smarty_tpl->getVariable('request')->value->ln;?>
', path:'<?php echo $_smarty_tpl->getVariable('request')->value->path;?>
', session_exp:<?php echo $_smarty_tpl->getVariable('session_exp')->value;?>
, server_time:'<?php echo date("F d, Y H:i:s");?>
'});
		gw_adm_sys.init();
	</script>
</head>