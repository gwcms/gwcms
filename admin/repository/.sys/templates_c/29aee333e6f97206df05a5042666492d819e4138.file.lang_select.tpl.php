<?php /* Smarty version Smarty-3.0.7, created on 2012-09-17 07:32:41
         compiled from "/var/www/gw_cms/admin/templates/tools/lang_select.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7658244745056a7e95b1802-53631144%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '29aee333e6f97206df05a5042666492d819e4138' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/tools/lang_select.tpl',
      1 => 1347849804,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7658244745056a7e95b1802-53631144',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_gw_link')) include '/var/www/gw_cms/admin/lib/smarty/plugins/function.gw_link.php';
?><div class="change_lang" style="margin-bottom:20px;text-align:right">
<?php echo $_smarty_tpl->getVariable('lang')->value['LANGUAGE'];?>
: 

			<?php $_smarty_tpl->tpl_vars['curr_lang'] = new Smarty_variable((($tmp = @$_GET['lang'])===null||$tmp==='' ? GW::$static_conf['LANGS'][0] : $tmp), null, null);?>
			
			<?php  $_smarty_tpl->tpl_vars['ln_code'] = new Smarty_Variable;
 $_from = GW::$static_conf['LANGS']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['ln_code']->key => $_smarty_tpl->tpl_vars['ln_code']->value){
?>
				<?php if ($_smarty_tpl->tpl_vars['ln_code']->value==$_smarty_tpl->getVariable('curr_lang')->value){?><?php $_smarty_tpl->tpl_vars['tag_params'] = new Smarty_variable(array('class'=>'selected'), null, null);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['tag_params'] = new Smarty_variable('', null, null);?><?php }?>
				<?php echo smarty_function_gw_link(array('params'=>array('lang'=>$_smarty_tpl->tpl_vars['ln_code']->value),'title'=>$_smarty_tpl->getVariable('lang')->value['LANG'][$_smarty_tpl->getVariable('ln_code')->value],'tag_params'=>$_smarty_tpl->getVariable('tag_params')->value),$_smarty_tpl);?>

			<?php }} ?>
</div>

<?php if ($_smarty_tpl->getVariable('item')->value&&$_smarty_tpl->getVariable('item')->value->id){?>
	<script>
	
	var itemform_values='';
	var form_data_saver_enabled=true;
	
	$(function(){
		itemform_values = $('#itemform').serialize();
	})
				
	$(window).bind('beforeunload', function(){
	
		if(form_data_saver_enabled && (itemform_values != $('#itemform').serialize()) )
			return "Ar tikrai norite palikti puslapi ir prarasti pakeitimus?"
		
	});	
	
	function remove_form_data_saver()
	{
		//itemform_values = $('#itemform').serialize();
		form_data_saver_enabled=false;
	}
			
	</script>
<?php }?>