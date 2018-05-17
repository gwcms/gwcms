{function name=dl_output_filters_short_time}
	<span title="{$item->$field}">{$app->fh()->shortTime($item->$field)}</span>
{/function}


{assign var=dl_output_filters_truncate_size value=80 scope=global}
{*
	can change this value by adding bellow line to your list_template
	{$dl_output_filters_truncate_size=70}
*}

{function name=dl_output_filters_truncate}
	{$item->$field|escape|truncate:$dl_output_filters_truncate_size}
{/function}	


{function name=dl_output_filters_options}
	{if isset($options[$field][$item->$field])}
		{$options[$field][$item->$field]}
	{else}
		<span title="{$item->$field|escape}">-</span>
	{/if}
{/function}	