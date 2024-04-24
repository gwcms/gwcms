{$addlitag=1}
{if $m->write_permission}	
	{*
	{list_item_action_m url=[false,[act=>doRefund,id=>$item->id]] iconclass="fa fa-reply" confirm=1 caption=GW::l('/g/VIEWS/doRefund')}
	{list_item_action_m url=[false,[act=>doUpdate,id=>$item->id]] iconclass="fa fa-refresh" confirm=1 caption=GW::l('/g/VIEWS/doUpdate')}
	*}
	
	{list_item_action_m href="{$ln}/direct/orders/orders?act=doPayseraRetryProcess&id={$item->id}" iconclass="fa fa-refresh" caption="Retry payment accept"}
	
{/if}
	
	
