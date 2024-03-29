{$addlitag=1}
	{list_item_action_m url=["`$item->id`", [act=>doClone, id=>$item->id]] iconclass="fa fa-files-o text-mint" caption=GW::l('/g/VIEWS/doClone')}
	{dl_actions_delete}

	{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id]] iconclass="fa fa-file-o" caption=GW::l('/m/VIEWS/invoice')}
	{list_item_action_m url=["`$item->id`/preinvoice", [id=>$item->id]] iconclass="fa fa-file-o" caption=GW::l('/m/VIEWS/preinvoice')}

	
	{if $m->write_permission}
		
		{if $item->pay_status != 7}
			{list_item_action_m 
				url=[false,[act=>doMarkAsPayd,id=>$item->id]] 
				iconclass="fa fa-legal text-danger" 
				query_param=[rcv_amount,GW::l('/m/ENTER_EXACT_RECEIVED_AMOUNT')]
				caption=GW::l('/m/VIEWS/doMarkAsPayd')

			}
		{/if}

	
		{list_item_action_m url=[false,[act=>doOrderPaydNotifyUser,id=>$item->id,preview=>1]] iconclass="fa fa-cog"  caption="Preview/Send confirmation email"}
	{/if}

	{if $app->user->isRoot()}
		{list_item_action_m url=["`$item->id`/invoice", [id=>$item->id,html=>1]] iconclass="fa fa-file-o text-danger" caption="{GW::l('/m/VIEWS/invoice')} - html (root)"}
		{if $m->write_permission}
			{list_item_action_m url=[false,[act=>doSaveInvoice,id=>$item->id]] iconclass="fa fa-cog text-danger"  caption="Gen. invoice vars(root)"}
		{/if}
	{/if}


	
{if $m->write_permission}	
		
	{list_item_action_m 
		url=["`$item->id`/orderitems/0/form",[clean=>2]] 
		iconclass="fa fa-plus" action_addclass="iframe-under-tr"  
		caption="Add item"}


	{list_item_action_m url=["`$item->id`/mailS1"] iconclass="fa fa-envelope-o" caption="Siųsti klientui laišką ..."}
	{list_item_action_m url=["`$item->id`/form", ['insert_time'=>1]] iconclass="fa fa-calendar" caption="Keisti sąskaitos datą"}
{/if}


{if $m->feat(itax)}
	<li class="divider"></li>
	{list_item_action_m url=[false,[act=>doItaxSync,id=>$item->id]] iconclass="fa fa-cloud-upload" caption="Suvesti į Itax [D]"  shift_button=1}
	
	{if $item->get('itax_status_ex/purchase')==7}
		{list_item_action_m url=[false,[act=>doItaxCancel,id=>$item->id]] iconclass="fa fa-window-close text-danger" confirm=1 caption="Šalinti iš itax(Pirkimo sąsk.)"}
	{/if}
{/if}
