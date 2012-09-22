<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:35
         compiled from "/var/www/gw_cms/admin/templates/list/actions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16629800165056a7e37307d3-80119126%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '465d41385c13986fe18eaf94f6124d95ae0d6331' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/actions.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16629800165056a7e37307d3-80119126',
  'function' => 
  array (
    'dl_actions_move' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_actions_delete' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_actions_edit' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_actions_invert_active' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_display_actions' => 
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
?><?php if (!function_exists('smarty_template_function_dl_actions_move')) {
    function smarty_template_function_dl_actions_move($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_actions_move']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_function_gw_link(array('do'=>"move",'icon'=>"action_move_up",'params'=>array('id'=>$_smarty_tpl->getVariable('item')->value->id,'where'=>'up'),'show_title'=>0),$_smarty_tpl);?>

	<?php echo smarty_function_gw_link(array('do'=>"move",'icon'=>"action_move_down",'params'=>array('id'=>$_smarty_tpl->getVariable('item')->value->id,'where'=>'down'),'show_title'=>0),$_smarty_tpl);?>
<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>


<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><?php if (!function_exists('smarty_template_function_dl_actions_delete')) {
    function smarty_template_function_dl_actions_delete($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_actions_delete']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_function_gw_link(array('do'=>"delete",'icon'=>"action_file_delete",'params'=>array('id'=>$_smarty_tpl->getVariable('item')->value->id),'show_title'=>0,'confirm'=>1),$_smarty_tpl);?>
<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>


<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><?php if (!function_exists('smarty_template_function_dl_actions_edit')) {
    function smarty_template_function_dl_actions_edit($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_actions_edit']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_function_gw_link(array('relative_path'=>($_smarty_tpl->getVariable('item')->value->id)."/form",'icon'=>"action_edit",'show_title'=>0),$_smarty_tpl);?>
<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>


<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><?php if (!function_exists('smarty_template_function_dl_actions_invert_active')) {
    function smarty_template_function_dl_actions_invert_active($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_actions_invert_active']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_function_gw_link(array('do'=>"invert_active",'icon'=>"active_".($_smarty_tpl->getVariable('item')->value->active),'params'=>array('id'=>$_smarty_tpl->getVariable('item')->value->id),'show_title'=>0),$_smarty_tpl);?>
<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>


<?php if (!function_exists('smarty_template_function_dl_display_actions')) {
    function smarty_template_function_dl_display_actions($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_display_actions']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php  $_smarty_tpl->tpl_vars['button_func'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('dl_actions')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['button_func']->key => $_smarty_tpl->tpl_vars['button_func']->value){
?>
		<?php $tmp = "smarty_template_function_"."dl_actions_".($_smarty_tpl->tpl_vars['button_func']->value); $tmp($_smarty_tpl,array());?>

	<?php }} ?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

