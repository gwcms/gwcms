{$addlitag=1}


{list_item_action_m url=["`$item->id`/crop"] iconclass="fa fa-crop"}

{*
{if $item->encrypted}
	
	{list_item_action_m url=[false,[act=>doDecrypt,id=>$item->id]] iconclass="fa fa-unlock" caption="Decrypt"}
	
{/if}
*}

<li class="divider"></li>


{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}


