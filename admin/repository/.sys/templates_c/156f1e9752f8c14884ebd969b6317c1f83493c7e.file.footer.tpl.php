<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/footer.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19638196225056a7e97ebfc7-50242543%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '156f1e9752f8c14884ebd969b6317c1f83493c7e' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/footer.tpl',
      1 => 1336700913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19638196225056a7e97ebfc7-50242543',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

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

	
</div>