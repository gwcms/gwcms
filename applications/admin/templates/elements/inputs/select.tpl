{if $empty_option}
	{$options=$lang.EMPTY_OPTION+$options}
{/if}

<select  id="{$id}" class="selectpicker {if $class} {$class}{/if}" {if $required}required="required"{/if} name="{$input_name}" onchange="{$onchange}" 
		 {if $enable_search}data-live-search="true"{/if}
		 >
	{html_options  selected=$value options=$options}
</select>


{*{$m->addIncludes("bs/select", 'js', "`$app_root`static/vendor/bootstrap-select/js.js")}*}


{if !$gwcms_input_select_loaded}
	{$m->addIncludes("bs/selectcss", 'css', "`$app_root`static/vendor/bootstrap-select/css.css")}
	<script src="{$app_root}vendor/bootstrap-select/js.js"></script>
	<script type="text/javascript">$(function(){ $('.selectpicker').selectpicker(); });</script>	
	<script type="text/javascript">require(['vendor/bootstrap-select/js'], function(){ $('.selectpicker').selectpicker(); });</script>
	
	
	{assign var=gwcms_input_select_loaded value=1 scope=global}	
	
	
{/if}
