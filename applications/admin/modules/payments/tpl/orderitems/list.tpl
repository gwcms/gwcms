{extends file="default_list.tpl"}




{block name="init"}


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

	{$dl_filters=[]}

	{if $smarty.get.noactions}
		{$dl_actions=[]}
	{else}
		{$dl_actions=[delete,edit]}
	{/if}
	
	{$dl_smart_fields=[obj_id,group_id,user_title]}


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
	
	


	
	


	{capture append=footer_hidden}	
		
	
	{/capture}
	
	{if !$smarty.get.noactions}
		{$dl_checklist_enabled=1}
		{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}	
		{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri('dialoggroupduplicates')}', $(this).find(':selected').text())">{GW::l('/A/VIEWS/dialoggroupduplicates')}</option>{/capture}	
	{/if}		
	
{/block}


