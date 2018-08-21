{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if strpos($field, 'title_')===0}
		{include file=$i name=$field}
	{elseif $field=='owner_id'}
		{include file=$i name=$field type=number}
	{else}
		{include file=$i name=$field type=read}
	{/if}

{/function}


