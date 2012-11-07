<?php /* Smarty version Smarty-3.0.7, created on 2012-11-07 18:19:23
         compiled from "/var/www/gw_cms/admin/templates/default_close.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2411567245056a7e97da372-39457669%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2d7024ba003ca3fa6796bc5f651044d32aa90556' => 
    array (
      0 => '/var/www/gw_cms/admin/templates/default_close.tpl',
      1 => 1349098715,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2411567245056a7e97da372-39457669',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!$_GET['clean']){?>
<br /><br />

        <span class="cleaner"></span>
    </div>
    <div id="push"></div>
</div>

<div id="footer">
    <?php $_template = new Smarty_Internal_Template_Custom("footer.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php unset($_template);?>
</div>

<?php }?>

</body>
</html>