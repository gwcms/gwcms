{if $textarea_tabfunc}
	<script type="text/javascript" src="{$app_root}static/js/textarea_tabfunc.js"></script>	
{/if}
{if $autoresize}
	<script>
		require(['gwcms'], function(){
			require(['js/jq/autoresize.jquery.min']);
		})
		
	</script>
{/if}
{if $expandonfocus}
	<script>
		require(['gwcms'], function(){

			$("#{$id}").focus(function(){	
				$(this).css({ height: "{$expandonfocus}" })
			}).blur(function(){
				$(this).css({ height: "{$height}" })
			})
		})
	</script>	
{/if}
<textarea  
	id="{$id}" 
	class="form-control{if $autoresize} ta_autoresize{/if} inp-textarea" 
	name="{$input_name}" 
	{if $tabs}onkeydown="return catchTab(this,event)"{/if} 
	style="{foreach $style as $attr => $val}{$attr}:{$val};{/foreach} width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" 
	{if $rows}rows="{$rows}"{/if}  
	{if $readonly}readonly="readonly"{/if}
	onchange="this.value=$.trim(this.value);" 
	{if $hidden_note}title="{$hidden_note}"{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
>{$value|escape}</textarea>