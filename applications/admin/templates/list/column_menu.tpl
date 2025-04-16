


{include "list/actions.tpl"}
{$action_class=gwcmsLinksInDD}


{$addlitag=1}

{if $m->write_permission}
	{list_item_action_m url=[false, [act=>doMultiSetValue,field=>$smarty.get.field]] iconclass="fa fa-pencil-square" caption=GW::l('/A/VIEWS/doMultiSetValue')}
	{list_item_action_m url=[false, [act=>doDragMoveSorting,field=>$smarty.get.field]] iconclass="fa fa-arrows" caption=GW::l('/A/VIEWS/doDragMoveSorting')}


	{list_item_action_m url=[false, [act=>doFillSeries,field=>$smarty.get.field]] iconclass="fa fa-arrows" caption=GW::l('/A/VIEWS/doFillSeries')}
	
	{list_item_action_m url=[false, [act=>doCopyColValues,field=>$smarty.get.field]] iconclass="fa fa-pencil-square" caption=GW::l('/A/VIEWS/doCopyColValues')}
{/if}	




{list_item_action_m href="javascript:gwcms.addFilters('{$smarty.get.field}');" iconclass="fa fa-search" caption=GW::l('/A/VIEWS/doFilterByColumn')}




{*{include "`$m->tpl_dir`/addlistconfig.tpl"}*}
