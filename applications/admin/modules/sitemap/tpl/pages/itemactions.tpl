{$addlitag=1}


{list_item_action_m url=["`$item->id`/form", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
{list_item_action_m 
	url=[false,[act=>doPreview,id=>$item->id]] 
	iconclass="fa fa-external-link" 
	tag_params=[target=>'_blank'] caption=GW::l('/m/VIEWS/doPreview')}


	
{list_item_action_m url=[false,[act=>doExportTree,id=>$item->id,opts=>[alllns=>1,content=>1]]] iconclass="fa fa fa-upload" caption=GW::l('/g/VIEWS/export') shift_button=1}


{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
	{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 shift_button=1 caption=GW::l('/g/VIEWS/doDelete')}
	
	{if $app->user->isRoot()}
		{list_item_action_m url=[false,[act=>doWriteLock,id=>$item->id]] iconclass="fa fa-lock text-danger" confirm=1 shift_button=1 caption=GW::l('/g/VIEWS/doWriteLock')}
	{/if}
{else}
	{if $app->user->isRoot()}
		{list_item_action_m url=[false,[act=>doWriteUnLock,id=>$item->id]] iconclass="fa fa-unlock text-danger" confirm=1 shift_button=1 caption=GW::l('/g/VIEWS/doWriteUnLock')}
	{/if}
{/if}





