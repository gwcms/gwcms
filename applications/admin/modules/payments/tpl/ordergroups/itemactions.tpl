{$addlitag=1}
	{list_item_action_m url=["`$item->id`", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
	{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}

	{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id]] iconclass="fa fa-file-o" caption=GW::l('/m/VIEWS/invoice')}

	
	
	{if $item->pay_status != 7}
		{list_item_action_m 
			url=[false,[act=>doMarkAsPayd,id=>$item->id]] 
			iconclass="fa fa-legal text-danger" 
			query_param=[rcv_amount,GW::l('/m/ENTER_EXACT_RECEIVED_AMOUNT')]
			caption=GW::l('/m/VIEWS/doMarkAsPayd')
			
		}
	{/if}


{if $app->user->isRoot()}
		{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id,html=>1]] iconclass="fa fa-file-o" caption="{GW::l('/m/VIEWS/invoice')} - html (root)"}
	{list_item_action_m url=[false,[act=>doSaveInvoice,id=>$item->id]] iconclass="fa fa-cog text-danger"  caption="Gen. invoice vars(root)"}
	
	{list_item_action_m url=[false,[act=>doOrderPaydNotifyUser,id=>$item->id]] iconclass="fa fa-cog text-danger"  caption="Send confirmation email (root)"}
{/if}