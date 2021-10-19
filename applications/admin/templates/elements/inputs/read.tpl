
{if $value_options}
	{if is_array($value)}
		{*multiselect*}
		{foreach $value as $val}
			{if isset($value_options[$val])}{$value_options[$val]}{else}{$val}{/if}{if !$val@last},{/if}
		{/foreach}
	{else}
		{if isset($value_options[$value])}
			{$value_options[$value]}
		{else}
			{$value}
		{/if}		
	{/if}
	

{else}
	
	{if $is_link}
		<a href="{$value|escape}" title="{$value|escape}" target="_blank">{$value|escape|truncate:40}</a>
	{else}
		{if is_object($value)}
			{json_encode($value, $smarty.const.JSON_PRETTY_PRINT)}
		{else}
			{$value}
		{/if}
	{/if}
{/if}