
{*text line or password*}
<input id="{$id}"
	class="form-control{if $class} {$class}{/if} "
	{if $required}required="required"{/if} 
	name="{$input_name}" 
	type="{$type|default:text}" 
	value="{$value|escape}" 
	onchange="this.value=$.trim(this.value);" 
	{if $readonly}readonly{/if}
	{if $maxlength}maxlength="{$maxlength}"{/if} 
	style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}" 
	{if $hidden_note}title="{$hidden_note}"{/if} 
	{if $placeholder}placeholder="{$placeholder}"{/if} 
	{$input_extra_params}
/>
