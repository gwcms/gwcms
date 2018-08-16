{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if strpos($field, 'title_')===0}
		{include file="elements/input.tpl" name=$field}
	{else}
		{include file="elements/input.tpl" name=$field type=read}
	{/if}

{/function}


