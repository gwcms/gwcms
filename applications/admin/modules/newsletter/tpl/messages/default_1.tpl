{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[title=>1, subject_lt=>1, sender=>1,recipients_total=>1,sent_count=>1,insert_time=>1,update_time=>1]}
	
	{$dl_smart_fields=[recipients_total]}	
	

	{function dl_cell_recipients_total}
		{$item->recipients_count}
	{/function}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[edit,delete,clone,send]}
	
	
	{function name=dl_actions_send}
		{if ! $item->sent_count}
			{gw_link do="send" icon="email_go" params=[id=>$item->id] show_title=0 confirm=1 title="Send email"}
		{/if}
	{/function}
		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}