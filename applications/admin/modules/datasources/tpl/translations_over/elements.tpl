{function name="cust_inputs"}
	{if $field=='module'}
		{call e field=module type=select empty_option=1 options=$options.module}
	{elseif $field=="fullkey"}
		
		{if $smarty.get.form_ajax}
			{call e field=$field}
		{else}
			{call e field=$field type=select_ajax 	
			maximumSelectionLength=1
			options=[]
			preload=1
			datasource=$app->buildUri('datasources/translations/keysearch')}			
		{/if}
		

	
	{elseif strpos($field, "value_")===0}
			{call e field=$field type=textarea height=50px}	
	{elseif $field=="context_group" || $field=="context_id"}
		{if $smarty.get.form_ajax}
			{call e field=$field type=read}
		{else}
			{call e field=$field}
		{/if}
	{elseif $field=='value'}
		{call e field=value type=textarea height=50px i18n=4 i18n_expand=1}
	{/if}
{/function}


