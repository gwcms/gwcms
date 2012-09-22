<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:35
         compiled from "/var/www/gw_cms/admin/templates/list/output_filters.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9207143395056a7e378f833-28781276%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '04e7cc2ffc086e3c62594996cd4fcb3d348ac391' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/list/output_filters.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9207143395056a7e378f833-28781276',
  'function' => 
  array (
    'dl_output_filters_short_time' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'dl_output_filters_truncate' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
  ),
  'has_nocache_code' => 0,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!function_exists('smarty_template_function_dl_output_filters_short_time')) {
    function smarty_template_function_dl_output_filters_short_time($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_output_filters_short_time']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<span title="<?php echo $_smarty_tpl->getVariable('item')->value->{$_smarty_tpl->getVariable('field')->value};?>
"><?php echo FH::shortTime($_smarty_tpl->getVariable('item')->value->{$_smarty_tpl->getVariable('field')->value});?>
</span><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>



<?php $_smarty_tpl->tpl_vars['dl_output_filters_truncate_size'] = new Smarty_variable(80, null, 3);?>

<?php if (!is_callable('smarty_modifier_truncate')) include '/var/www/gw_cms/admin/lib/smarty/plugins/modifier.truncate.php';
?><?php if (!function_exists('smarty_template_function_dl_output_filters_truncate')) {
    function smarty_template_function_dl_output_filters_truncate($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->template_functions['dl_output_filters_truncate']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
	<?php echo smarty_modifier_truncate($_smarty_tpl->getVariable('item')->value->{$_smarty_tpl->getVariable('field')->value},$_smarty_tpl->getVariable('dl_output_filters_truncate_size')->value);?>
<?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
	