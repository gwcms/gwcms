{function name="cust_inputs"}
	{if $field==''}
		
	{elseif $field=="admin_name"}
		{call e name=admin_name type=text}
	{elseif $field=="title"}
		{call e type=text i18n=4}
	{elseif $field=="admin_email"}
		{call e type=text}
	{elseif $field=="hosts"}
		{call e type=tags placeholder=GW::l('/m/ADD_HOST')}
	{elseif $field=="admin_host"}
			
	{elseif $field=="key"}
		{call e type=text}	
	{elseif $field=="langs"}
		{call e type=multiselect options=array_merge(GW::s('LANGS'),GW::s('i18nExt')) sorting=1 options_fix=1}
	{else}
		{call e type=read}
	{/if}
		
{/function}