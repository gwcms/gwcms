<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:19:23
         compiled from "/var/www/gw_cms/admin/modules/adm_users/tpl/login/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:880552862509a8a0b1e24f9-81109715%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9afa38c65d4ff35cb29795b79015db880b40e1c6' => 
    array (
      0 => '/var/www/gw_cms/admin/modules/adm_users/tpl/login/default.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '880552862509a8a0b1e24f9-81109715',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php $_template = new Smarty_Internal_Template_Custom("head.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<body>

<div id="wrap">
<div id="header">
<div class="space1"><a href="#" title=""><?php echo $_smarty_tpl->getVariable('lang')->value['SITE_TITLE'];?>
</a></div>
</div>

<div id="sidebar"></div>

<div id="content"><?php if ($_SESSION['messages']){?> <?php $_template = new Smarty_Internal_Template_Custom("messages.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?> <br />
<?php }?>


<form method="POST" id="lgn_frm" action="<?php echo $_smarty_tpl->getVariable('request')->value->uri;?>
"><input
	type="hidden" name="act" value="do_login" />

<table class="login_frm">
	<tr>
		<td style="padding-right: 10px"><?php echo $_smarty_tpl->getVariable('lang')->value['USER'];?>
</td>
		<td><input class="lgn_inpt_vart<?php if ($_smarty_tpl->getVariable('login_error')->value){?>err<?php }?>"
			name="login[0]" value="<?php echo $_COOKIE['login_0'];?>
" /></td>
	</tr>
	<tr>
		<td style="padding-right: 10px"><?php echo $_smarty_tpl->getVariable('lang')->value['PASS'];?>
</td>
		<td><input class="lgn_inpt_pwd<?php if ($_smarty_tpl->getVariable('login_error')->value){?>err<?php }?>"
			name="login[1]" type="password" /></td>
	</tr>

	<?php if ($_smarty_tpl->getVariable('autologin')->value){?>
	<tr>
		<td style="padding-right: 10px"><?php echo $_smarty_tpl->getVariable('lang')->value['AUTOLOGIN'];?>
</td>
		<td><input name="login_auto" type="checkbox" /></td>
	</tr>
	<?php }?> <?php if (count(GW::$static_conf['LANGS'])>1){?>
	<tr>
		<td><?php echo $_smarty_tpl->getVariable('lang')->value['LANGUAGE'];?>
:</td>
		<td><select name="ln">
			<?php  $_smarty_tpl->tpl_vars['ln_code'] = new Smarty_Variable;
 $_from = GW::$static_conf['ADMIN_LANGS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['ln_code']->key => $_smarty_tpl->tpl_vars['ln_code']->value){
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['ln_code']->value;?>
"
				<?php if ($_COOKIE['login_ln']==$_smarty_tpl->tpl_vars['ln_code']->value){?>SELECTED<?php }?>>
			<?php echo $_smarty_tpl->getVariable('lang')->value['LANG'][$_smarty_tpl->getVariable('ln_code')->value];?>
</option>
			<?php }} ?>
		</select></td>
	</tr>
	<?php }?>

	<tr>
		<td></td>
		<td><input class="submit_btn" type="submit"
			value="<?php echo $_smarty_tpl->getVariable('lang')->value['DOLOGIN'];?>
" /></td>
	</tr>


</table>
</form>

<script type="text/javascript">
	$('input[name=login[0]]').focus();
</script> <?php $_template = new Smarty_Internal_Template_Custom("default_close.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>