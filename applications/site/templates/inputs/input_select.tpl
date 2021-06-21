<select 
	name="{$input_name}" 
	class="form-control{if $addclass} {$addclass}{/if}" 
	id="{$id}" {if $multiple}multiple="multiple"{/if} {if $onchange}onchange="{$onchange}" onkeyup="$(this).change()"{/if}
	{if $required}required="required"{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	data-empty-option="{GW::ln('/g/EMPTY_OPTION_TITLE')}"
	{if $multiple}data-sorting="1"{/if}
	{if $readonly}disabled="disabled"{/if}
	>
	{if $empty_option || $empty_option_title}<option value="">{if $empty_option_title}{$empty_option_title}{else}{GW::ln('/g/EMPTY_OPTION_TITLE')}{/if}</option>{/if}
	
	{html_options options=$options selected=$value}
</select>
	
{if $onchange}
<script>
	$(function(){
		$('#{$id}').change();
	})
</script>
{/if}