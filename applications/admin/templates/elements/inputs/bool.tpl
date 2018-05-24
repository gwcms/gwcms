<input 
	{if $hidden_note}title="{$hidden_note}"{/if} 
	type="checkbox" 
	{if $value}CHECKED{/if} 
	onclick="$(this).next().val(this.checked ? 1 : 0).change();{if $onchange_function}{$onchange_function}('{$onchange_function_arg}', this.checked){/if}" 
/>
<input  id="{$id}" type="hidden" name="{$input_name}" value="{$value|escape}" class="gwcheckboxinput"/>


{if $stateToggleRows}
	<script>
		require(['gwcms'], function(){
			$('#{$id}').change(function(){		
				$('.{$stateToggleRows}').toggle(this.value=='1')
			}).change();
		});
	</script>
{/if}
