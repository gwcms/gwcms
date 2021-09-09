{extends file="default_list.tpl"}




{block name="init"}

	
	{$dl_inline_edit=1}
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}


	{function name=do_toolbar_buttons_orderacts} 		
		{*
		{toolbar_button iconclass="fa fa-money"
				title=GW::ln('/m/VIEWS/doCreateInvoices')
				href=$m->buildUri(false, [act=>doCreateInvoices])}
		*}
		{toolbar_button iconclass="fa fa-money"
				title=GW::l('/m/VIEWS/paymentsummary')
				href=$m->buildUri(paymentsummary)}		
	{/function}
	

	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,dialogconf2,print,orderacts]}		
	
	
	{$display_fields=['tour_part_id'=>0]}
	
	{$dl_smart_fields=[user_title,relations,user_id,admin_id,instruments]}	

	
	{$dl_actions=[items,invoice,editshift,ext_actions]}
	{$dl_group_list_by=['tour_part_id']}
	
	
	{function dl_actions_items}
		{$url=$m->buildUri("`$item->id`/orderitems",[clean=>2])}

		{list_item_action_m href=$url action_addclass="iframe-under-tr" title="Cart items" caption="Items({$item->items_count})"}
	{/function}	

	{function dl_prepare_item}
		{if !$item->approved}{$item->set(row_class,notapproved)}{/if}
	{/function}
	
		
	

	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$options.user_id[$item->user_id]->title}</a>
	{/function}	
	{function dl_cell_user_id}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$item->user_id}</a>
	{/function}
	{function dl_cell_admin_id}
		<a class="iframeopen" href="{$app->buildUri("users/usr/`$item->admin_id`/form",[clean=>2])}" title="Admin info">{$item->admin_id}</a>
	{/function}		
	

	{function dl_actions_invoice}
		{if $item->isdir==0}
			{list_item_action_m 
				url=[invoice,[id=>$item->id,clean=>1]] iconclass="fa fa-file-o" action_addclass="iframe-under-tr"
				tag_params=['data-iframeopt'=>'{"width":"1000px","height":"600px"}']
			}
		{/if}
	{/function}		
	
	

	{capture append=footer_hidden}	
		
	
	{/capture}	


	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}	
	
	
	
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	{$dl_output_filters.changetrack=changetrack}
	
{/block}


