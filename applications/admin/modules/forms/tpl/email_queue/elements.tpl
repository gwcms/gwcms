{function name="cust_inputs"}
	
	
	{$i="elements/input.tpl"}


	{if $field==''}
		
	{elseif in_array($field, [to,from,subject,status])}	
		{call e field=$field type=text}
	{elseif $field=="body"}
		
		{if $item->plain}
			{call e field=$field type=textarea height=100px}
		{else}
			{call e field=$field type=htmlarea layout=wide}
		{/if}
	
	{elseif $field=="plain"}
		{call e field=$field type=bool}
	{else}
		{call e field=$field type=read}
	{/if}

{/function}


