{*text line or password*}
<input id="{$id}"
	class="form-control{if $class} {$class}{/if} inp-{$type|default:text}"
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
	 {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
	{$input_extra_params}
/>
