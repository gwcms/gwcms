<input 
	{if $hidden_note}title="{$hidden_note}"{/if} 
	type="checkbox" 
	{if $value}CHECKED{/if} 
	{if $readonly}disabled="disabled"{/if}
	onclick="$(this).next().val(this.checked ? 1 : 0).change();{if $onchange_function}{$onchange_function}('{$onchange_function_arg}', this.checked){/if}" 
/>
<input  id="{$id}" type="hidden" name="{$input_name}" value="{$value|escape}" class="gwcheckboxinput" />


{if $stateToggleRows || $onchange}
	<script>
		require(['gwcms'], function(){
			$('#{$id}').change(function(){	
				{if $stateToggleRows}$('.{$stateToggleRows}').toggle(this.value=='1');$('.{$stateToggleRows}_inv').toggle(this.value!='1');{/if}
				{if $onchange}
					var ln = $(this).parents('.ln_contain:first').attr('title')
					var state = $(this).val()==1;
					{$onchange}
				{/if}
			}).change();
		});
	</script>
{/if}
