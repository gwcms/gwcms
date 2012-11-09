<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:05
         compiled from "/var/www/gw_cms/admin/templates/default_open.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4829174145056a7e936a2a5-73656285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d7e0c025a0e487299f887a03f1f879c92cbcd97' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_open.tpl',
      1 => 1352303821,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4829174145056a7e936a2a5-73656285',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_gw_display_plugins')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_display_plugins.php';
?><?php $_template = new Smarty_Internal_Template_Custom("head.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<body>

<?php if (!$_GET['clean']){?>

<div id="wrap">
    <div id="header">
        <div class="space1">
            <a href="#" title=""><?php echo $_smarty_tpl->getVariable('lang')->value['SITE_TITLE'];?>
</a>
        </div>
        
	<?php $_template = new Smarty_Internal_Template_Custom("submenu.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	
        <div id="login-info">
        	<b><?php echo $_smarty_tpl->getVariable('lang')->value['LOGGED_AS'];?>
:</b> 
        	<a href="<?php echo $_smarty_tpl->getVariable('request')->value->ln;?>
/adm_users/profile"><?php echo (($tmp = @GW::$user->get('name'))===null||$tmp==='' ? GW::$user->get('username') : $tmp);?>
 
        	 
        	</a> 
        	<?php if ($_SESSION['cms_auth']['switchUser']){?>
        	<a href="<?php echo $_smarty_tpl->getVariable('request')->value->ln;?>
/adm_users?act=do:switch_user_return"  style="font-weight:normal;color:orange">
        		<?php $_smarty_tpl->tpl_vars['sw_usr_return'] = new Smarty_variable(GW::$user->find(array('id=?',$_SESSION['cms_auth']['switchUser'])), null, null);?>
        		(<?php echo sprintf($_smarty_tpl->getVariable('lang')->value['SWITCH_USER_RETURN'],$_smarty_tpl->getVariable('sw_usr_return')->value->name);?>
)
        	</a>
        	<?php }?>
        	| 
            	<a href="<?php echo $_smarty_tpl->getVariable('request')->value->ln;?>
/adm_users/login/logout" id="logout"><?php echo $_smarty_tpl->getVariable('lang')->value['LOGOUT'];?>
</a>
            	
            	<?php $_smarty_tpl->tpl_vars['new_messages'] = new Smarty_variable(GW::$user->countNewMessages(), null, null);?>
            	<?php if ($_smarty_tpl->getVariable('new_messages')->value){?>
	            	<br>
	            	<a href="<?php echo $_smarty_tpl->getVariable('ln')->value;?>
/config/messages"><font color="#ffff99"><?php echo $_smarty_tpl->getVariable('new_messages')->value;?>
</font> new messages</a>
            	<?php }?>
		</div>
    </div>

    <div id="sidebar">
		<?php $_template = new Smarty_Internal_Template_Custom("menu.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
		
		<?php echo smarty_function_gw_display_plugins(array('id'=>"after_menu"),$_smarty_tpl);?>

		
    </div>

	<?php $_template = new Smarty_Internal_Template_Custom("breadcrumbs.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	<?php if ($_smarty_tpl->getVariable('toolbar')->value){?><?php $_template = new Smarty_Internal_Template_Custom("toolbar.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?><?php }?>
    
    <div id="content">

<?php }?>

<?php $_template = new Smarty_Internal_Template_Custom("messages.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('log')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
	<?php if ($_smarty_tpl->tpl_vars['item']->value){?>
		<?php echo dump($_smarty_tpl->tpl_vars['item']->value);?>

	<?php }?>
<?php }} ?>


