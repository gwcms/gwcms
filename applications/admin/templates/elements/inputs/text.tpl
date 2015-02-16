
{*text line or password*}
<input {if $class}class="{$class}"{/if} {if $required}required="required"{/if} name="{$input_name}" type="{$type}" value="{$value|escape}" onchange="this.value=$.trim(this.value);" {if $readonly}readonly{/if}
{if $maxlength}maxlength="{$maxlength}"{/if} style="width: {$width|default:"100%"};" {if $hidden_note}title="{$hidden_note}"{/if} />
