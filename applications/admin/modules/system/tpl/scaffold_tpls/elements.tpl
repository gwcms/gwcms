


{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if $field==''}
		
	{*inputsdrop*}
	
	{else}
		
	
		{include file=$i name=$field type=read}
	{/if}
		
{/function}