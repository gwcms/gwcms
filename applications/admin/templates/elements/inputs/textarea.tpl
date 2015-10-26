{if $tabs}
	<script type="text/javascript" src="{$app_root}js/textarea_tabfunc.js"></script>	
{/if}


{if $autoresize}
	
	{if !isset($autoresize_single)}
	{assign var=autoresize_single value="1" scope="global"}
	<script type="text/javascript" src="{$app_root}js/autoresize.jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){ $('.ta_autoresize').autoResize(); });
	</script>
	{/if}
{/if}


<textarea  id="{$id}" {if $autoresize}class="ta_autoresize"{/if} name="{$input_name}" {if $tabs}onkeydown="return catchTab(this,event)"{/if} 
style="width: {$width|default:"100%"}; {if !$rows}height: {$height|default:"250px"};{/if}" {if $rows}rows="{$rows}"{/if} 
onchange="this.value=$.trim(this.value);" {if $hidden_note}title="{$hidden_note}"{/if}>{$value|escape}</textarea>
    			
