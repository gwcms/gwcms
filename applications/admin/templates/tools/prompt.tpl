
{include file="elements/input_func.tpl"} 

{*
{if isset($smarty.get.act)}
	{GW::ln("/g/VIEWS/`$smarty.get.act`")}
{/if}
*}

<h6>{$prompt_title}</h6>

<form id="promptform" class="promptform" action="{$smarty.server.REQUEST_URI}" method="{$method|default:get}"  enctype="multipart/form-data"  >
	
	{foreach $smarty.get as $key =>$val}
		{if $key=='url'}{continue}{/if}
		{if is_array($val) || is_object($val)}{$val=json_encode($val, true)}{/if}
		<input type="hidden" name="{$key|escape}" value="{$val|escape}">
	{/foreach}
	
	

{include "tools/form_components.tpl"}

<table >
	{call "build_form" fields_config=$prompt_fields}
</table>

<br>

<button class='btn btn-success'>{GW::l('/g/SUBMIT')}</button>
</form>


<style>
	.promptform .inp_select_ajax{ min-width: 150px; }
</style>

