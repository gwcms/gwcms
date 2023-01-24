

{$addlitag=true}

<li class="divider"></li>

{dl_actions_clone}
{dl_actions_delete}


{if $item->user_id}
	{list_item_action_m url=[false,[act=>doSyncWithLinked,id=>$item->id]] iconclass="fa fa-upload" confirm=1 caption=GW::l('/m/VIEWS/doSyncWithLinked')}
{/if}


{if $item->doc_id}
	{list_item_action_m href="/{$item->ln}/direct/docs/docs/item?id={$item->doc->key}&answerid={$item->id}" iconclass="fa fa-globe text-mint" caption="Suformuotas dokumentas"}
{/if}

	
		