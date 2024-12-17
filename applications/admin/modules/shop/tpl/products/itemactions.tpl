{$addlitag=true}

{if $smarty.get.frontend}
	{dl_actions_edit}
{/if}

{if $m->features.modifications}
	{if $item->parent_id}{$tmp="iframe-under-tr"}{else}{$tmp=""}{/if}
	{list_item_action_m url=[false,[act=>doCreateModification,id=>$item->id]] iconclass="fa fa-clone" caption=GW::l('/m/VIEWS/doCreateModification') action_addclass=$tmp  }
{/if}


<li class="divider"></li>

{if !$smarty.get.frontend}
{dl_actions_clone}
{dl_actions_delete}
{/if}





