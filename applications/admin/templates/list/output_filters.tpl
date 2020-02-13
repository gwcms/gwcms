{function name=dl_output_filters_short_time}
	<span title="{$val}">{$app->fh()->shortTime($val)}</span>
{/function}

{function name=dl_output_filters_expand_truncate}
		{$expand_truncate_size=$expand_truncate_size|default:40}
		{if mb_strlen($val) > $expand_truncate_size}
			<a class="showsenders" href='#' onclick='$(this).find(".togl").toggle();return false' style="max-width:250px;display:inline-block">
				{mb_substr($val,0,$expand_truncate_size)}
				<span class="togl">...</span>
				<span class="togl" style="display:none;">{mb_substr($val,$expand_truncate_size,mb_strlen($val))}</span>
			</a> 
		{else}
			{$item->$field}
		{/if}
{/function}



{*{assign var=dl_output_filters_truncate_size value=80 scope=global}*}
{*
	can change this value by adding bellow line to your list_template
	{$dl_output_filters_truncate_size=70}
*}

{function name=dl_output_filters_truncate}
	{$tmp=$dl_output_filters_truncate_size|default:80}
	{$val|escape|truncate:$tmp}
{/function}	


{function name=dl_output_filters_options}
	{if isset($options[$field][$val])}
		{$options[$field][$val]}
	{else}
		<span title="id:{$val|escape}">-</span>
	{/if}
{/function}	

{function name=dl_output_filters_obj_options}
	{if is_array($val)}
		{$ids=$val}
	{else}
		{$ids=[$val]}
	{/if}
	{foreach $ids as $id}
		{if isset($options[$field][$id])}
			{$options[$field][$id]->get($dl_output_filters_args[$field][titlefield]|default:title)}
		{else}
			<span title="{$id|escape}">-</span>
		{/if}	
	{/foreach}
{/function}	

{function name=dl_output_filters_array}
	{$val=json_encode($val)}
	{call "dl_output_filters_expand_truncate"}
{/function}	
