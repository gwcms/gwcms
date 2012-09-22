<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:35
         compiled from "/var/www/gw_cms/admin/templates/list/toolbar_buttons.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20827557095056a7e369e204-58759460%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4ca3228dbf2e2dae648ebda9035901ba94bc8ce' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/toolbar_buttons.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20827557095056a7e369e204-58759460',
  'function' => 
  array (
    'dl_toolbar_buttons_addnew' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_toolbar_buttons_filters' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_toolbar_buttons_info' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_toolbar_buttons_dialogconf' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_display_toolbar_buttons' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
  ),
  'has_nocache_code' => 0,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><?php if (!function_exists('smarty_template_function_dl_toolbar_buttons_addnew')) {
    function smarty_template_function_dl_toolbar_buttons_addnew($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_toolbar_buttons_addnew']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_function_gw_link(array('relative_path'=>'form','title'=>$_smarty_tpl->getVariable('lang')->value['CREATE_NEW'],'icon'=>"action_file_add",'params'=>array('id'=>0)),$_smarty_tpl);?>

	&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>


<?php if (!function_exists('smarty_template_function_dl_toolbar_buttons_filters')) {
    function smarty_template_function_dl_toolbar_buttons_filters($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_toolbar_buttons_filters']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php if ($_smarty_tpl->getVariable('dl_filters')->value){?>
		<img src="img/icons/search.png"  align="absmiddle" onclick="$(this).next().click()" /> 
		<a href="#show_filters" onclick="$('#filters').toggle();return false"><?php echo $_smarty_tpl->getVariable('lang')->value['SEARCH'];?>
</a>	
		&nbsp;&nbsp;&nbsp;
	<?php }?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
	


<?php if (!function_exists('smarty_template_function_dl_toolbar_buttons_info')) {
    function smarty_template_function_dl_toolbar_buttons_info($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_toolbar_buttons_info']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php if ($_smarty_tpl->getVariable('page')->value->notes){?>
		<img src="img/icons/action_info.png"  align="absmiddle" onclick="$(this).next().click()" /> 
		<a href="#show_about" onclick="open_notes(<?php echo $_smarty_tpl->getVariable('page')->value->id;?>
);return false"><?php echo $_smarty_tpl->getVariable('lang')->value['ABOUT'];?>
</a>	
		&nbsp;&nbsp;&nbsp;
		
		<div id="dialog-message" title="<?php echo $_smarty_tpl->getVariable('lang')->value['ABOUT'];?>
 <?php echo $_smarty_tpl->getVariable('page')->value->title;?>
" style="display:none"></div>
	<?php }?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

	
<?php if (!function_exists('smarty_template_function_dl_toolbar_buttons_dialogconf')) {
    function smarty_template_function_dl_toolbar_buttons_dialogconf($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_toolbar_buttons_dialogconf']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<script>
		function lds_config()
		{
			gw_dialog.open('<?php echo $_smarty_tpl->getVariable('ln')->value;?>
/<?php echo GW::$request->path;?>
/dialogconfig', { width:400 })
		}
	</script>
	<img src="img/icons/settings.png"  align="absmiddle" onclick="$(this).next().click()" /> 
	<a href="#" onclick="lds_config();return false"><?php echo $_smarty_tpl->getVariable('lang')->value['LIST_DISPLAY_SETTINGS'];?>
</a>	
	&nbsp;&nbsp;&nbsp;<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
	
	
<?php if (!function_exists('smarty_template_function_dl_display_toolbar_buttons')) {
    function smarty_template_function_dl_display_toolbar_buttons($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_display_toolbar_buttons']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php  $_smarty_tpl->tpl_vars['button_func'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dl_toolbar_buttons')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['button_func']->key => $_smarty_tpl->tpl_vars['button_func']->value){
?>
		<?php $tmp = "smarty_template_function_"."dl_toolbar_buttons_".($_smarty_tpl->tpl_vars['button_func']->value); $tmp($_smarty_tpl,array());?>

	<?php }} ?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

