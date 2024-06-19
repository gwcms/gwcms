{extends file="default_list.tpl"}




{block name="init"}

	{if $smarty.get.groupby}
		{$dl_group_list_by=[$smarty.get.groupby]}
	{/if}
		
	

	{if $list}
		{$do_toolbar_buttons=[]}
	{else}
		{$do_toolbar_buttons=[addnew]}
	{/if}
	
	
	{function name=do_toolbar_buttons_rootact}
		{toolbar_button title="doCaptureObjInvoiceLines (root)" iconclass='gwico-Export text-error' href=$m->buildUri(false,[act=>doCaptureObjInvoiceLines])}	
	{/function}

	
	
	{if !$smarty.get.clean}
		{$do_toolbar_buttons=[addnew]}
		{$do_toolbar_buttons[] = hidden}
		{$do_toolbar_buttons[] = search}
		{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
		
		
		{if $app->user->isRoot()}
			{$do_toolbar_buttons_hidden[]=rootact}
		{/if}		
		
		{$dl_calc_totals.total=0}
	{/if}	
	
	
	
	{$dl_inline_edit=1}
	
	
	{if $m->cartgroup_id}
		{$dl_filters=[]}
	{/if}
	
	{function dl_actions_invoice}
		{if $item->isdir==0}
			{list_item_action_m 
				url=["../ordergroups/invoice",[id=>$item->group_id,clean=>1]] iconclass="fa fa-file-o" action_addclass="iframe-under-tr"
				tag_params=['data-iframeopt'=>'{"width":"1000px","height":"600px"}']
			}
		{/if}
	{/function}	
	

	{if $smarty.get.noactions}
		{$dl_actions=[]}
	{else}
		{$dl_actions=[editshift,invoice]}
		
		{if $app->user->isRoot()}
			{$dl_actions[]=ext_actions}
		{/if}
	{/if}
	
	{$dl_smart_fields=[obj_id,group_id,user_title,user_email,door_code,coupon_codes,contracts,vat_group,obj_type,status]}


	{function dl_cell_obj_id}
		{if $item->modpath}
			<a class="iframeopen" href="{$app->buildUri("{$item->modpath}/{$item->obj_id}/form")}">{$item->obj_id}</a>
		{else}
			{$item->obj_id}
		{/if}
	{/function}
	
	{function dl_cell_group_id}
		<a target='_blank' href="{$app->buildUri("payments/ordergroups/{$item->group_id}/form")}">{$item->group_id}</a>
	{/function}	
	
	{function dl_cell_obj_type}
		{$options.obj_type[$item->obj_type]}
	{/function}		
	
	{function dl_cell_door_code}
		{*add flds=',door_code'*}
		<a target='_blank' href="{$app->buildUri("system/ttlock/{$item->get('keyval/door_code_id')}/form")}">{$item->get('keyval/door_code_id')}</a>
	{/function}		
	
	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info - {$options.user_id[$item->user_id]->title}">{$options.user_id[$item->user_id]->title}</a>
	{/function}		
	
	{function dl_cell_user_email}
		{$options.user_id[$item->user_id]->email}
	{/function}		
		
	{function dl_cell_coupon_codes}
		{foreach $item->coupon_codes as $id}
			<a href="{$app->buildUri("payments/discountcode/{$id}/form")}">{$id}</a>
		{/foreach}
	{/function}		

	{function dl_cell_contracts}
		{if $contract_counts[$item->id]}
			<a class="badge bg-info iframe-under-tr" href="{$app->buildUri("forms/answers",[obj_type=>$m->model->table,obj_id=>$item->id,clean=>2])}">{$contract_counts[$item->id]}</a>
		{else}
			-
		{/if}
	{/function}	
	
	
	
	{function dl_cell_vat_group}
		{$item->vat_title}
	{/function}	
	{function dl_cell_status}
		{if $item->status}
			{GW::ln("/M/orders/status/`$item->status`")}
		{else}
			-
		{/if}
	{/function}	
	
	

	{capture append=footer_hidden}	
		
	
	{/capture}
	
	{if !$smarty.get.noactions}
		{$dl_checklist_enabled=1}
		{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}	
		{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doMarkAsProcessed])}', 1)">{GW::l('/A/VIEWS/doMarkAsProcessed')}</option>{/capture}	
	{/if}		
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	{$dl_output_filters.pay_time=short_time}
	
{/block}


