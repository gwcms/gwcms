{$width=185px}
<style>
	.filterinput{ max-width: 185px; }
</style>

    <a  id='filterSpecialsInfo' class="fa gwAddPopover add-popover" 
    data-content="Special codes: <li>#empty# for empty string</li><li>#zero# for 0</li>"  
    data-placement="right" data-container="body" data-toggle="popover" data-html="true" href="#popover" 
    style="float:right;{if !$m->list_params.filters}display:none{/if}"
    onclick="return false"></a>

{if $filters_directload}	


	{foreach $m->list_params.filters as $filter}
		{include file="elements/input_filter.tpl" name=$filter.field params=$m->list_config.dl_filters[$filter.field] value=$filter.value compare_type=$filter.ct class=filterinput}
	{/foreach}
{else}
	{foreach $dl_filters as $filter}
		{if $smarty.get.value}{$value=$smarty.get.value}{/if}
		{include file="elements/input_filter.tpl" name=$filter@key params=$filter muted=!$filter}
	{/foreach}
	
	
	{include "includes.tpl"}
{/if}
