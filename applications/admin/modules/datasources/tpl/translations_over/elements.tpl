{function name="cust_inputs"}
	
	{$i="elements/input.tpl"}	

	{if $field=='module'}
		{include file="elements/input.tpl" name=module type=select empty_option=1 options=$options.module}
	{elseif $field=="fullkey"}
		
		{if $smarty.get.form_ajax}
			{include file="elements/input.tpl" name=$field}
		{else}
			{include file="elements/input.tpl" name=$field type=select_ajax 	
			maximumSelectionLength=1
			options=[]
			preload=1
			datasource=$app->buildUri('datasources/translations/keysearch')}			
		{/if}
		

	
	{elseif strpos($field, "value_")===0}
			{include file="elements/input.tpl" type=textarea height=50px name=$field}	
	{elseif $field=="context_group" || $field=="context_id"}
		{if $smarty.get.form_ajax}
			{include file="elements/input.tpl" name=$field type=read}
		{else}
			{include file="elements/input.tpl" name=$field}
		{/if}
	{elseif $field=='value'}
		{include file="elements/input.tpl" name=value type=textarea height=50px i18n=4 i18n_expand=1}
	{/if}

{/function}


