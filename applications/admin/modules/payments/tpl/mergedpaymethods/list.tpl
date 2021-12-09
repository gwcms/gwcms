{extends file="default_list.tpl"}


{block name="init"}

	
		
	
	
	

	{$do_toolbar_buttons[] = dialogconf}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}		
	{$do_toolbar_buttons[] = search}		
	
	{$dl_actions=[edit,invert_active_ajax]} {*ext_actions*}
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}	
	{$dl_dragdropmove=1}


	{$dl_inline_edit=1}	
	
	{function dl_cell_logo}
		{if $item->logo}
			<img src="{$item->logo}" style="height:20px">
		{/if}
	{/function}
	
	{$dl_smart_fields=[logo]}
	
	
	
	{$disabled_groups=array_flip(json_decode($m->modconfig->disabled_group,true))}
	{function name=dl_prepare_item}
		{if !$item->active || isset($disabled_groups[$item->group])}
			{$item->set('row_class', 'gw_notactive')}
		{/if}
	{/function}	

	{capture append=footer_hidden}

	<style>
		.gw_notactive img, .gw_notactive a{ opacity: 0.4 }
	</style>
	{/capture}	
{/block}