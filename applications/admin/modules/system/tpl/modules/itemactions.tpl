{$addlitag=true}

{list_item_action_m url=["`$item->id`/form",[id=>$item->id]] iconclass="fa fa-pencil-square-o" caption=GW::l('/g/VIEWS/form')}


<li class="divider"></li>

{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}



{list_item_action_m url=["`$item->id`/page_views",[id=>$item->id]] iconclass="fa fa-list" caption=GW::l('/m/MAP/childs/page_views/title')}
{list_item_action_m url=["`$item->id`/module_fields",[id=>$item->id]] iconclass="fa fa-object-ungroup" caption=GW::l('/m/MAP/childs/module_fields/title')}
