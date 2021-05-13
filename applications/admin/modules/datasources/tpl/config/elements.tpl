{function name="cust_inputs"}
	
	{if 1==0}
		
	{elseif $field==value}
		{call e type=text}
	{elseif $field==key}
		{call e type=text}
	{else}
		{call e type=read}
	{/if}
{/function}


