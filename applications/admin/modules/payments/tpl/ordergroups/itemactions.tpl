{$addlitag=1}
	{list_item_action_m url=["`$item->id`", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
	{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1 caption=GW::l('/g/REMOVE')}

	{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id]] iconclass="fa fa-file-o" caption=GW::l('/m/VIEWS/invoice')}



{if $app->user->isRoot()}
		{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id,html=>1]] iconclass="fa fa-file-o" caption="{GW::l('/m/VIEWS/invoice')} - html (root)"}
	{list_item_action_m url=[false,[act=>doSaveInvoice,id=>$item->id]] iconclass="fa fa-cog text-danger"  caption="Gen. invoice vars(root)"}
{/if}