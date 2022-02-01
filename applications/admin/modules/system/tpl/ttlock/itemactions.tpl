{$addlitag=true}







{if !$item->remote_id}
	{list_item_action_m url=[false,[act=>doRemoteCreate,id=>$item->id]] iconclass="fa fa-cog" caption="Send code to device"}
{else}
	{list_item_action_m url=[false,[act=>doRemoteDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption="Remove from device"}
{/if}

