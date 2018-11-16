{function name="cust_inputs"}
	
	
	{$i="elements/input.tpl"}


	{if $field==''}
		
	{elseif in_array($field, [to,from,subject,error])}	
		{include file="elements/input.tpl" name=$field type=text}
	{elseif $field=="body"}
		
		{if $item->plain}
			{include file="elements/input.tpl" name=$field type=textarea height=100px}
		{else}
			{include file="elements/input.tpl" name=$field type=htmlarea layout=wide}
		{/if}
	
	{elseif $field=="plain"}
		{include file="elements/input.tpl" name=$field type=bool}
	{else}
		{include file="elements/input.tpl" name=$field type=read}
	{/if}

{/function}


