{extends file="default_list.tpl"}




{block name="init"}

	
	{*
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}		
	*}
	
	{if $list}
		{$do_toolbar_buttons=[]}
	{else}
		{$do_toolbar_buttons=[addnew]}
	{/if}
	
	
	{$dl_inline_edit=1}

	{$dl_filters=[]}

	{$dl_actions=[delete,edit]}
	{$dl_smart_fields=[obj_id,user_title]}		
	{$dl_output_filters=[catalog_type=>options,tonality=>options]}


	{function dl_cell_obj_id}
		{if $item->modpath}
			<a class="iframeopen" href="{$app->buildUri("{$item->modpath}/{$item->obj_id}/form")}">{$item->obj_id}</a>
		{else}
			{$item->obj_id}
		{/if}
	{/function}
	
	{function dl_cell_user_title}
		<a class="iframeopen" href="{$app->buildUri("customers/users/`$item->user_id`/form",[clean=>2,readonly=>1])}" title="Vartotojo info">{$options.user_id[$item->user_id]->title}</a>
	{/function}		



	
	


	{capture append=footer_hidden}	
		
	
	{/capture}
	
	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}	
	{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri('dialoggroupduplicates')}', $(this).find(':selected').text())">{GW::l('/A/VIEWS/dialoggroupduplicates')}</option>{/capture}	
			
	
{/block}


