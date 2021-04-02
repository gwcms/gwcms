{function name="cust_inputs"}
	
	{if in_array($field,[title,aka])}
		{call e type=text}
	{elseif $field==type}
		{call e type=select options=$options.classtypes empty_option=1 default=$m->filters.type}
	{elseif $field==active}
		{call e type=bool}
	{else}
		{call e type=read}
	{/if}
{/function}


