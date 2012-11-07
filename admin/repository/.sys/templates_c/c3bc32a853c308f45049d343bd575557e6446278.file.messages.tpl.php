<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:19:23
         compiled from "/var/www/gw_cms/admin/templates/messages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12455913275056a7e957cf98-84480669%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c3bc32a853c308f45049d343bd575557e6446278' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/messages.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12455913275056a7e957cf98-84480669',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
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

<?php }?>