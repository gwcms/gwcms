{$width=185px}
{if $filters_directload}	
	
	
	{foreach $m->list_params.filters as $filter}
	
		{include file="elements/input_filter.tpl" name=$filter.field params=$dl_filters[$filter.field] value=$filter.value compare_type=$filter.ct}
	{/foreach}
{else}
	{foreach $dl_filters as $filter}
		{if $filter}
			{include file="elements/input_filter.tpl" name=$filter@key params=$filter}
		{/if}
	{/foreach}


	{include "includes.tpl"}
{/if}





		