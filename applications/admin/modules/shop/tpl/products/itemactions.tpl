{$addlitag=true}

{if $m->features.modifications}
	{list_item_action_m url=[false,[act=>doCreateModification,id=>$item->id]] iconclass="fa fa-clone" caption=GW::l('/m/VIEWS/doCreateModification')}
{/if}


<li class="divider"></li>

{dl_actions_clone}
{dl_actions_delete}





