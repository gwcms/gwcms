{extends file="default_list.tpl"}


{block name="init"}

	{$dl_smart_fields=[status,hits,recipients_ids,recipients_count,attachments]}
	
	{$dl_filters=[]}	
	
		
	{$do_toolbar_buttons[] = hidden}	
	{$do_toolbar_buttons_hidden = [dialogconf,modinfo]}	
	
	{$do_toolbar_buttons[] = search}
	{$dl_output_filters=[update_time=>short_time,insert_time=>short_time]}
	
	
	
	
	
	
	{$dl_actions=[edit,invert_active,send,ext_actions]}
	
	
	{function dl_actions_send}
		{if $item->status < 11}
				
			{if $item->active}
				{list_item_action_m url=[false,[act=>doSend,id=>$item->id]] confirm=1 iconclass="fa fa-send" title="Send email"} 
			{/if}
		{else}
			{list_item_action_m url=[sentinfo,[id=>$item->id]] iconclass="fa fa-info-circle" title=$m->lang.VIEWS.sentinfo} 
		{/if}
	{/function}
	
	{function dl_cell_status}
		{$m->lang.OPT.status[$item->status]}
		
		{if $item->sent_count}
			
		{/if}
	{/function}
	
	{function dl_cell_hits}
		{if $item->hit_count}
			{gw_link relative_path="`$item->id`/hits" params=[id=>$item->id] title=$item->hit_count}
		{else}
			-
		{/if}
	{/function}	
	
	
	{function dl_cell_recipients_ids}
		{$tmp = $item->recipients_ids}
		{if is_array($tmp)}({count($tmp)}){/if}
	{/function}		
	
	{function dl_cell_recipients_count}
		{foreach $item->getActiveLangs() as $ln}
			{$ln}: {$item->get(recipients_count, $ln)}
		{/foreach}
	{/function}
	
	{function name=dl_cell_attachments}
		{$tmp=$item->extensions.attachments->count()}	
		{if $tmp}{$tmp}{else}{/if}
	{/function}	
		
	
	

		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	
{/block}