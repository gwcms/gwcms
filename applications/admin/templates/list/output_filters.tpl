{function name=dl_output_filters_short_time}
	<span title="{$item->$field}">{$app->fh()->shortTime($item->$field)}</span>
{/function}

{function name=dl_output_filters_expand_truncate}
		{if mb_strlen($item->$field) > 40}
			<a class="showsenders" href='#' onclick='$(this).find(".togl").toggle();return false' style="max-width:250px;display:inline-block">
				{mb_substr($item->$field,0,40)}
				<span class="togl">...</span>
				<span class="togl" style="display:none;">{mb_substr($item->$field,40,mb_strlen($item->$field))}</span>
			</a> 
		{else}
			{$item->$field}
		{/if}
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

{function name=dl_output_filters_obj_options}
	{if is_array($item->$field)}
		{$ids=$item->$field}
	{else}
		{$ids=[$item->$field]}
	{/if}
	{foreach $ids as $id}
		
		{if isset($options[$field][$id])}
			{$options[$field][$id]->get($dl_output_filters_args[$field][titlefield]|default:title)}
		{else}
			<span title="{$id|escape}">-</span>
		{/if}	
	{/foreach}
{/function}	