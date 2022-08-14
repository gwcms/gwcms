


{include "list/actions.tpl"}
{$action_class=gwcmsLinksInDD}


{$addlitag=1}


{list_item_action_m url=[false, [act=>doMultiSetValue,field=>$smarty.get.field]] iconclass="fa fa-pencil-square" caption=GW::l('/A/VIEWS/doMultiSetValue')}
{list_item_action_m url=[false, [act=>doDragMoveSorting,field=>$smarty.get.field]] iconclass="fa fa-arrows" caption=GW::l('/A/VIEWS/doDragMoveSorting')}



{*{include "`$m->tpl_dir`/addlistconfig.tpl"}*}
