{if $value_options}
	{if isset($value_options[$value])}
		{$value_options[$value]}
	{else}
		{$value}
	{/if}
{else}
	
	{if $is_link}
		<a href="{$value|escape}" title="{$value|escape}" target="_blank">{$value|escape|truncate:40}</a>
	{else}
		{$value}
	{/if}
{/if}