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
	
	{if !$smarty.get.clean}
		{$do_toolbar_buttons=[addnew]}
		{$do_toolbar_buttons[] = hidden}
		{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}		
	{/if}	
	
	
	
	{$dl_inline_edit=1}

	{if $m->cartgroup_id}
		{$dl_filters=[]}
	{/if}

	{if $smarty.get.noactions}
		{$dl_actions=[]}
	{else}
		{$dl_actions=[editshift]}
	{/if}
	
	{$dl_smart_fields=[obj_id,group_id,user_title,user_email]}


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
	
	
	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$options.user_id[$item->user_id]->title}</a>
	{/function}		
	
	{function dl_cell_user_email}
		{$options.user_id[$item->user_id]->email}
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


