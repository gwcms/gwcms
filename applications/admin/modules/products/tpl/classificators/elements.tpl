{function name="cust_inputs"}
	
	{if in_array($field,[title,aka])}
		{call e type=text}
	{elseif $field==type}
		{call e type=select options=$options.classtypes empty_option=1}
	{else}
		{call e type=read}
	{/if}
{/function}


