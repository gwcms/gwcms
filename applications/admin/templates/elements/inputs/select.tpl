{include file="elements/inputs/select_plain.tpl" class="`$class` selectpicker"}

{*{$m->addIncludes("bs/select", 'js', "`$app_root`static/vendor/bootstrap-select/js.js")}*}



{if !$gwcms_input_select_loaded}
	{$m->addIncludes("bs/selectcss", 'css', "`$app_root`static/vendor/bootstrap-select/css.css")}
	
	
	{assign var=gwcms_input_select_loaded value=1 scope=global}	
{/if}

<script type="text/javascript">require(['vendor/bootstrap-select/js'], function(){ $('.selectpicker').selectpicker(); });</script>
