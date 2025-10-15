{extends file="default_list.tpl"}


{block name="init"}


	{$do_toolbar_buttons = search}
	
	
	
	
	{$dl_actions=[edit]}
	
		
	
	
	{$dl_output_filters=[
		msg=>expand_truncate,
		err=>expand_truncate,
		insert_time=>short_time, 
		update_time=>short_time]}
		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}	
	{$dl_smart_fields=[diff]}
	
	{function dl_cell_diff}
		{if $item->content==diffc}
			{htmlspecialchars($item->uncompressDiff())|truncate:80}
		{else}
			{htmlspecialchars($item->diff)|truncate:80}
		{/if}
		
	{/function}
	

{/block}


