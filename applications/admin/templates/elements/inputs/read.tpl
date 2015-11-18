{if $options}
	{if isset($options[$value])}
		{$options[$value]}
	{else}
		{$value}
	{/if}
{else}
	{$value}
{/if}