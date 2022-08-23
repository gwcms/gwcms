{$addlitag=true}

{list_item_action_m url=["`$item->id`/form",[id=>$item->id]] iconclass="fa fa-pencil-square-o" caption=GW::l('/g/VIEWS/form')}


<li class="divider"></li>

{dl_actions_clone}
{dl_actions_delete}



{list_item_action_m url=["`$item->id`/page_views",[id=>$item->id]] iconclass="fa fa-list" caption=GW::l('/m/MAP/childs/page_views/title')}
{list_item_action_m url=["`$item->id`/module_fields",[id=>$item->id]] iconclass="fa fa-object-ungroup" caption=GW::l('/m/MAP/childs/module_fields/title')}
