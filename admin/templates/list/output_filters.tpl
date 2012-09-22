{function name=dl_output_filters_short_time}
	<span title="{$item->$field}">{FH::shortTime($item->$field)}</span>
{/function}


{assign var=dl_output_filters_truncate_size value=80 scope=global}
{*
	can change this value by adding bellow line to your list_template
	{$dl_output_filters_truncate_size=70}
*}

{function name=dl_output_filters_truncate}
	{$item->$field|truncate:$dl_output_filters_truncate_size}
{/function}	