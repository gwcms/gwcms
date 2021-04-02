{function name="cust_inputs"}
	
	{if in_array($field,[title,aka])}
		{call e type=text}
	{elseif $field==key}
		{call e type=text}
	{else}
		{call e type=read}
	{/if}
{/function}


