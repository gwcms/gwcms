

{$addlitag=true}

<li class="divider"></li>

{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}


{if !$item->options_src}
{list_item_action_m url=[false,[act=>doCreateClassificatorGroup,id=>$item->id]] iconclass="fa fa-plus-square-o" caption=GW::l('/g/doCreateClassificatorGroup')}
{/if}

