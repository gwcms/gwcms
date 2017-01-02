{if $tabs}
	<script type="text/javascript" src="{$app_root}js/textarea_tabfunc.js"></script>	
{/if}


{if $autoresize}
	<script>
		require(['gwcms'], function(){
			require(['js/jq/autoresize.jquery.min']);
		})
		
	</script>
{/if}


<textarea  id="{$id}" class="form-control{if $autoresize} ta_autoresize{/if}" name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
    			
 