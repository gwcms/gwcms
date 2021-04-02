{function name="cust_inputs"}
	
	{if in_array($field,[title,aka])}
		{call e type=text}
	{elseif $field==fields}
		{call e type=multiselect options=$options.fields}
	{else}
		{call e type=read}
	{/if}
{/function}


