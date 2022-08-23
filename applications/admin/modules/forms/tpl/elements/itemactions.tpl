

{$addlitag=true}

<li class="divider"></li>

{dl_actions_clone}
{dl_actions_delete}


{if !$item->options_src}
{list_item_action_m url=[false,[act=>doCreateClassificatorGroup,id=>$item->id]] iconclass="fa fa-plus-square-o" caption=GW::l('/g/doCreateClassificatorGroup')}
{/if}

