{$addlitag=1}


{dl_actions_clone}
{list_item_action_m 
	url=[false,[act=>doPreview,id=>$item->id]] 
	iconclass="fa fa-external-link" 
	tag_params=[target=>'_blank'] caption=GW::l('/m/VIEWS/doPreview')}


	
{list_item_action_m url=[false,[act=>doExportTree,id=>$item->id,opts=>[alllns=>1,content=>1]]] iconclass="fa fa fa-upload" caption=GW::l('/g/VIEWS/export') shift_button=1}


{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
	{dl_actions_delete shift_button=1}

	
	{if $app->user->isRoot()}
		{list_item_action_m url=[false,[act=>doWriteLock,id=>$item->id]] iconclass="fa fa-lock text-danger" confirm=1 shift_button=1 caption=GW::l('/g/VIEWS/doWriteLock')}
	{/if}
{else}
	{if $app->user->isRoot()}
		{list_item_action_m url=[false,[act=>doWriteUnLock,id=>$item->id]] iconclass="fa fa-unlock text-danger" confirm=1 shift_button=1 caption=GW::l('/g/VIEWS/doWriteUnLock')}
	{/if}
{/if}





