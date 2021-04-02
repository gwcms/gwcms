{$addlitag=true}

{list_item_action_m url=[false,[act=>doCreateModification,id=>$item->id]] iconclass="fa fa-clone" caption=GW::ln('/m/VIEWS/doCreateModification')}



<li class="divider"></li>

{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}





