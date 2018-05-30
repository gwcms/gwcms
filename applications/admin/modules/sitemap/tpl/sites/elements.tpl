


{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if $field==''}
		
		{elseif $field=="admin_name"}
		{include file=$i name=admin_name type=text}
	{elseif $field=="title"}
		{include file=$i name=title type=text i18n=4}
	{elseif $field=="admin_email"}
		{include file=$i name=admin_email type=text}
	{elseif $field=="hosts"}
		{include file=$i name=hosts type=tags placeholder=GW::l('/m/ADD_HOST')}
	
	{else}
		
	
		{include file=$i name=$field type=read}
	{/if}
		
{/function}