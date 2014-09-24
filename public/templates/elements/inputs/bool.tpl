<input {if $hidden_note}title="{$hidden_note}"{/if} type="checkbox" {if $value}CHECKED{/if} onclick="$(this).next().val(this.checked ? 1 : 0)" />
<input type="hidden" name="{$input_name}" value="{$value|escape}" />
