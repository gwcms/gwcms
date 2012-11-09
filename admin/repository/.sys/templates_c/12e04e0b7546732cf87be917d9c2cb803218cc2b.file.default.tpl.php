<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:14
         compiled from "/var/www/gw_cms/admin/modules/sitemap/tpl/pages/default.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14486228875056a7e6479bb8-22662409%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '12e04e0b7546732cf87be917d9c2cb803218cc2b' => 
    array (
      0 => '/var/www/gw_cms/admin/modules/sitemap/tpl/pages/default.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '0b170a821b056d972af5f48b04cf7740d59de6f7' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_list.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '1d7e0c025a0e487299f887a03f1f879c92cbcd97' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_open.tpl',
      1 => 1352303821,
      2 => 'file',
    ),
    'd8a21e5610e678242dc26f988e0808985e7a4cf5' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/head.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '8c20bd38485ec4c30c0f48cd6f90984e0cd2202f' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/submenu.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    'daa6800c06674c0ffcb75a79577911b9fa125bcc' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/menu.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '30b19fc9151db3227c62488b62d3dbd9bc26f772' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/breadcrumbs.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '65f7bcd5989888e29d4afac7d823cae913ffabe0' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/toolbar.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    'c3bc32a853c308f45049d343bd575557e6446278' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/messages.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '65d05b43f7df9cca48e781a5613e8d609e57e863' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/page_by.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '9c1ef86bf7b37eb95f7d110ced5a8f9e3b9371cb' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/paging.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '2d7024ba003ca3fa6796bc5f651044d32aa90556' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_close.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
    '156f1e9752f8c14884ebd969b6317c1f83493c7e' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/footer.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14486228875056a7e6479bb8-22662409',
  'function' => 
  array (
    'dl_cell_ico' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_cell_title' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_cell_in_menu' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

		



		<?php $_template = new Smarty_Internal_Template_Custom("default_open.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '14486228875056a7e6479bb8-22662409';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:14
         compiled from "/var/www/gw_cms/admin/templates/default_open.tpl" */ ?>
<?php if (!is_callable('smarty_function_gw_display_plugins')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_display_plugins.php';
?><?php $_template = new Smarty_Internal_Template_Custom("head.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:14
         compiled from "/var/www/gw_cms/admin/templates/head.tpl" */ ?>
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
</head><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/head.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
<body>

<?php if (!$_GET['clean']){?>

<div id="wrap">
    <div id="header">
        <div class="space1">
            <a href="#" title=""><?php echo $_smarty_tpl->getVariable('lang')->value['SITE_TITLE'];?>
</a>
        </div>
        
	<?php $_template = new Smarty_Internal_Template_Custom("submenu.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:14
         compiled from "/var/www/gw_cms/admin/templates/submenu.tpl" */ ?>
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
</ul><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/submenu.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
	
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
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/menu.tpl" */ ?>
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

<?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/menu.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
		
		<?php echo smarty_function_gw_display_plugins(array('id'=>"after_menu"),$_smarty_tpl);?>

		
    </div>

	<?php $_template = new Smarty_Internal_Template_Custom("breadcrumbs.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/breadcrumbs.tpl" */ ?>

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

<?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/breadcrumbs.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
	<?php if ($_smarty_tpl->getVariable('toolbar')->value){?><?php $_template = new Smarty_Internal_Template_Custom("toolbar.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/toolbar.tpl" */ ?>
<div class="gw_toolbar">
	<table cellpadding=2px cellspacing=0>
	<tr>
	
	<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('toolbar')->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
		<td>
		<a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['link'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['onclick']){?>onclick="<?php echo $_smarty_tpl->tpl_vars['item']->value['onclick'];?>
"<?php }?> class="gw_button">
			<?php if ($_smarty_tpl->tpl_vars['item']->value['img']){?>
				<img src="<?php echo $_smarty_tpl->tpl_vars['item']->value['img'];?>
" align="absmiddle" />
			<?php }?>
			
			<?php if ($_smarty_tpl->tpl_vars['item']->value['title']){?><span class="gw_button_label"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</span><?php }?>
		</a>
		</td>
	<?php }} ?>
	
	<td style="border-right:1px solid silver;border-left:1px solid silver;padding:1px">
	
	</td>
	
	</tr></table>
</div><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/toolbar.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?><?php }?>
    
    <div id="content">

<?php }?>

<?php $_template = new Smarty_Internal_Template_Custom("messages.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '882311220509a8b2eebaec9-75604494';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/messages.tpl" */ ?>
<?php if ($_SESSION['messages']){?>

<?php $_smarty_tpl->tpl_vars['classes'] = new Smarty_variable(array(0=>'sbrsucc',1=>'sbrwarn',2=>'sbrerror',3=>'sbrinfo'), null, null);?>


<?php  $_smarty_tpl->tpl_vars['msg'] = new Smarty_Variable;
 $_from = $_SESSION['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['msg']->key => $_smarty_tpl->tpl_vars['msg']->value){
?>
	<?php $_smarty_tpl->tpl_vars['msg_type_id'] = new Smarty_variable($_smarty_tpl->tpl_vars['msg']->value[0], null, null);?>
	<div class="status_bx1 <?php echo $_smarty_tpl->getVariable('classes')->value[$_smarty_tpl->getVariable('msg_type_id')->value];?>
" style="display:none">
		<?php echo GW_Error_Message::read($_smarty_tpl->tpl_vars['msg']->value[1]);?>

	</div>
<?php }} ?>


<script>

$(document).ready(function() {
	$('.status_bx1').fadeIn("slow");
});


</script>

<?php echo GW::$request->removeMessages();?>


<br />

<?php }?><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/messages.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('log')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
	<?php if ($_smarty_tpl->tpl_vars['item']->value){?>
		<?php echo dump($_smarty_tpl->tpl_vars['item']->value);?>

	<?php }?>
<?php }} ?>


<?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/default_open.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>



<?php $_smarty_tpl->tpl_vars['dl_toolbar_buttons'] = new Smarty_variable(array('addnew','filters','info'), null, null);?>

<div>
	

	<?php $_smarty_tpl->tpl_vars['icons'] = new Smarty_variable(array(0=>'file',1=>'folder',2=>'link'), null, null);?>



	<?php if (!function_exists('smarty_template_function_dl_cell_ico')) {
    function smarty_template_function_dl_cell_ico($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_cell_ico']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
		<img src="img/icons/<?php echo $_smarty_tpl->getVariable('icons')->value[$_smarty_tpl->getVariable('item')->value->type];?>
.png" align="absmiddle" vspace="2" /><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

	<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><?php if (!function_exists('smarty_template_function_dl_cell_title')) {
    function smarty_template_function_dl_cell_title($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_cell_title']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
		<?php if ($_smarty_tpl->getVariable('item')->value->type!=2){?>
			<?php echo smarty_function_gw_link(array('params'=>array('pid'=>$_smarty_tpl->getVariable('id')->value),'title'=>$_smarty_tpl->getVariable('item')->value->title),$_smarty_tpl);?>

		<?php }else{ ?>
			<?php echo $_smarty_tpl->getVariable('item')->value->title;?>

		<?php }?>
		
		<?php if ($_smarty_tpl->getVariable('item')->value->child_count){?>
			(<?php echo $_smarty_tpl->getVariable('item')->value->child_count;?>
)
		<?php }?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

	
	<?php if (!function_exists('smarty_template_function_dl_cell_in_menu')) {
    function smarty_template_function_dl_cell_in_menu($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_cell_in_menu']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
			<?php  $_smarty_tpl->tpl_vars['ln_code'] = new Smarty_Variable;
 $_from = GW::$static_conf['LANGS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['ln_code']->key => $_smarty_tpl->tpl_vars['ln_code']->value){
?>
				<?php if ($_smarty_tpl->getVariable('item')->value->get("in_menu_".($_smarty_tpl->tpl_vars['ln_code']->value))){?><?php echo strtoupper($_smarty_tpl->tpl_vars['ln_code']->value);?>
<?php }?>
			<?php }} ?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

	
	<?php $_smarty_tpl->tpl_vars['display_fields'] = new Smarty_variable(array('ico'=>1,'path'=>1,'pathname'=>0,'title'=>1,'in_menu'=>1,'insert_time'=>1,'update_time'=>1), null, null);?>
	
	<?php $_smarty_tpl->tpl_vars['dl_fields'] = new Smarty_variable($_smarty_tpl->getVariable('m')->value->getDisplayFields($_smarty_tpl->getVariable('display_fields')->value), null, null);?>	
	<?php $_smarty_tpl->tpl_vars['dl_smart_fields'] = new Smarty_variable(array('title','in_menu','ico'), null, null);?>
	
	<?php $_smarty_tpl->tpl_vars['dl_output_filters'] = new Smarty_variable(array('insert_time'=>'short_time','update_time'=>'short_time'), null, null);?>	
	
	<?php if (!isset($_smarty_tpl->tpl_vars['dl_toolbar_buttons']) || !is_array($_smarty_tpl->tpl_vars['dl_toolbar_buttons']->value)) $_smarty_tpl->createLocalArrayVariable('dl_toolbar_buttons', null, null);
$_smarty_tpl->tpl_vars['dl_toolbar_buttons']->value[] = 'dialogconf';?>	
	
	
	<?php if ($_smarty_tpl->getVariable('m')->value->filters['parent_id']){?>
		<?php $_smarty_tpl->tpl_vars['dl_actions'] = new Smarty_variable(array('invert_active','move','edit','delete'), null, null);?>
	<?php }else{ ?>
		<?php $_smarty_tpl->tpl_vars['dl_actions'] = new Smarty_variable(array('invert_active','edit','delete'), null, null);?>
	<?php }?>
	
	<?php unset($_smarty_tpl->getVariable('display_fields')->value['ico']);?>

	
	<?php $_smarty_tpl->tpl_vars['dl_filters'] = new Smarty_variable($_smarty_tpl->getVariable('display_fields')->value, null, null);?>
	<?php $_smarty_tpl->tpl_vars['dl_order_enabled_fields'] = new Smarty_variable(array(), null, null);?>

	
	<?php $_smarty_tpl->tpl_vars['dl_smart_fields'] = new Smarty_variable(array_flip((($tmp = @$_smarty_tpl->getVariable('dl_smart_fields')->value)===null||$tmp==='' ? array() : $tmp)), null, null);?>

	<?php $_template = new Smarty_Internal_Template_Custom("list/toolbar_buttons.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	<?php $_template = new Smarty_Internal_Template_Custom("list/actions.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	<?php $_template = new Smarty_Internal_Template_Custom("list/output_filters.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
	
<table><tr><td>

	

	<table style="width:100%">
	<tr>
		<td>
			<?php smarty_template_function_dl_display_toolbar_buttons($_smarty_tpl,array());?>

		</td>
		
		<?php if ($_smarty_tpl->getVariable('m')->value->list_params['paging_enabled']&&count($_smarty_tpl->getVariable('list')->value)){?>
		<td	align="right" width="1%">
			<?php $_template = new Smarty_Internal_Template_Custom("list/page_by.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '14486228875056a7e6479bb8-22662409';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/list/page_by.tpl" */ ?>
<table class="gwTable" cellspacing="0" cellpadding="0">
	<tr>
		<?php $_template = new Smarty_Internal_Template_Custom("list/paging.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_smarty_tpl->assign('pages',$_template->getRenderedTemplate());?><?php unset($_template);?>
						
		<?php if ($_smarty_tpl->getVariable('paging_tpl_page_count')->value>1){?>
			<td><?php echo $_smarty_tpl->getVariable('pages')->value;?>
</td>
		<?php }?>
		<td>
			<table class="gw_clean_tbl" cellspacing="" cellpadding="1">
				<tr>
					<td nowrap class="fontsz5"><?php echo $_smarty_tpl->getVariable('lang')->value['PAGE_BY'];?>
:</td>
					<td>
						<form method="get" action="<?php echo $_SERVER['REQUEST_URI'];?>
" style="display:inline">
						<input onchange="this.form.submit()" name="list_params[page_by]" size=5 value="<?php echo $_smarty_tpl->getVariable('m')->value->list_params['page_by'];?>
" />
						<input type="hidden" name="list_params[page]" value="0" />
						</form>	
					</td>
				</tr>
			</table>
		</td>
		<?php if ($_smarty_tpl->getVariable('query_info')->value){?>
			<td nowrap>
				<table class="gw_clean_tbl" cellspacing="" cellpadding="1"><tr><td>
					<?php echo $_smarty_tpl->getVariable('lang')->value['ITEM_COUNT'];?>
: <b><?php echo $_smarty_tpl->getVariable('query_info')->value['item_count'];?>
</b>
				</td></tr></table>
			</td>
		<?php }?>
	</tr>
</table><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/list/page_by.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
		</td>
		<?php }?>
	</tr>
	</table>



</td></tr><tr><td>

<?php if (count($_smarty_tpl->getVariable('views')->value)>1){?>
	<?php $_template = new Smarty_Internal_Template_Custom("list/views.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php }?>


<?php if ($_smarty_tpl->getVariable('dl_filters')->value){?>
	<?php $_template = new Smarty_Internal_Template_Custom("list/filters.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
<?php }?>




</td></tr><tr><td>

<?php if (!count($_smarty_tpl->getVariable('list')->value)){?>
	<p><?php echo $_smarty_tpl->getVariable('lang')->value['NO_ITEMS'];?>
</p>
<?php }else{ ?>

<table class="gwTable gwActiveTable">


<tr>	
	
	<?php $_smarty_tpl->tpl_vars['dl_order_enabled_fields'] = new Smarty_variable(array_flip((($tmp = @$_smarty_tpl->getVariable('dl_order_enabled_fields')->value)===null||$tmp==='' ? array() : $tmp)), null, null);?>
	
	<?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dl_fields')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value){
?>
		<th>
			<?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable(FH::shortFieldTitle($_smarty_tpl->tpl_vars['field']->value), null, null);?>
			<?php if (isset($_smarty_tpl->getVariable('dl_order_enabled_fields',null,true,false)->value[$_smarty_tpl->getVariable('field',null,true,false)->value])){?>
				<?php $_template = new Smarty_Internal_Template_Custom("list/order.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->assign('name',$_smarty_tpl->tpl_vars['field']->value);$_template->assign('title',$_smarty_tpl->getVariable('title')->value); echo $_template->getRenderedTemplate();?><?php unset($_template);?>
			<?php }else{ ?>
				<?php echo $_smarty_tpl->getVariable('title')->value;?>

			<?php }?>
		</th>
	<?php }} ?>	
	<?php if (count($_smarty_tpl->getVariable('dl_actions')->value)){?>
		<th><?php echo $_smarty_tpl->getVariable('lang')->value['ACTIONS'];?>
</th>
	<?php }?>
</tr>

<?php $_smarty_tpl->tpl_vars['list_row_id'] = new Smarty_variable(0, null, null);?>

<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('list')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
?>
	<?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->getVariable('item')->value->id, null, null);?>
	<?php $_smarty_tpl->tpl_vars['list_row_id'] = new Smarty_variable($_smarty_tpl->getVariable('list_row_id')->value+1, null, null);?>
<tr id="list_row_<?php echo $_smarty_tpl->getVariable('list_row_id')->value;?>
" class="<?php if ($_smarty_tpl->getVariable('id')->value&&$_GET['id']==$_smarty_tpl->getVariable('id')->value){?>gw_active_row<?php }?>" 
	<?php if ($_smarty_tpl->getVariable('item')->value->list_color){?>style="background-color:<?php echo $_smarty_tpl->getVariable('item')->value->list_color;?>
"<?php }?>>
	
	
		<?php  $_smarty_tpl->tpl_vars['field'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dl_fields')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['field']->key => $_smarty_tpl->tpl_vars['field']->value){
?>
			<td>
				<?php if (isset($_smarty_tpl->getVariable('dl_smart_fields',null,true,false)->value[$_smarty_tpl->getVariable('field',null,true,false)->value])){?>
					<?php $tmp = "smarty_template_function_"."dl_cell_".($_smarty_tpl->tpl_vars['field']->value); $tmp($_smarty_tpl,array());?>

				<?php }elseif(isset($_smarty_tpl->getVariable('dl_output_filters',null,true,false)->value[$_smarty_tpl->getVariable('field',null,true,false)->value])){?>
					<?php $tmp = "smarty_template_function_"."dl_output_filters_".($_smarty_tpl->getVariable('dl_output_filters')->value[$_smarty_tpl->getVariable('field')->value]); $tmp($_smarty_tpl,array());?>

				<?php }else{ ?>
					<?php echo $_smarty_tpl->getVariable('item')->value->get($_smarty_tpl->tpl_vars['field']->value);?>

				<?php }?>
			</td>
		<?php }} ?>
		
		<?php if (count($_smarty_tpl->getVariable('dl_actions')->value)){?>
			<td nowrap>
				<?php smarty_template_function_dl_display_actions($_smarty_tpl,array());?>

			</td>
		<?php }?>

	 
</tr>

<?php }} ?>

</table>

<?php }?>

</td></tr></table>




</div>


		<?php $_template = new Smarty_Internal_Template_Custom("default_close.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '14486228875056a7e6479bb8-22662409';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/default_close.tpl" */ ?>
<?php if (!$_GET['clean']){?>
<br /><br />

        <span class="cleaner"></span>
    </div>
    <div id="push"></div>
</div>

<div id="footer">
    <?php $_template = new Smarty_Internal_Template_Custom("footer.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
$_template->properties['nocache_hash']  = '474467671509a8b2f593ea3-37321654';
$_tpl_stack[] = $_smarty_tpl; $_smarty_tpl = $_template;?>
<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:24:15
         compiled from "/var/www/gw_cms/admin/templates/footer.tpl" */ ?>

<div style="float:left">
	<?php echo $_smarty_tpl->getVariable('lang')->value['SERVER_TIME'];?>
: <span id="server_time"><?php echo date('H:i:s');?>
</span> 
	
	<br />
	<?php echo $_smarty_tpl->getVariable('lang')->value['YOUR_IP'];?>
: <?php echo $_SERVER['REMOTE_ADDR'];?>

</div>
<div style="float:left;margin-left:10px">
	<?php if ($_smarty_tpl->getVariable('session_exp')->value!=-1){?><span class="session_exp_t"><?php echo $_smarty_tpl->getVariable('lang')->value['SESSION_VALIDITY'];?>
:</span> 
	<span id="session_exp_t" class="session_exp_t">-</span>
	<?php }?>
</div>

<div style="float:right;text-align: right;">
	<?php echo str_replace('%year%',date('Y'),$_smarty_tpl->getVariable('lang')->value['FOOTER']);?>

	
</div><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/footer.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
</div>

<?php }?>

</body>
</html><?php $_smarty_tpl->updateParentVariables(0);?>
<?php /*  End of included template "/var/www/gw_cms/admin/templates/default_close.tpl" */ ?>
<?php $_smarty_tpl = array_pop($_tpl_stack);?><?php unset($_template);?>
