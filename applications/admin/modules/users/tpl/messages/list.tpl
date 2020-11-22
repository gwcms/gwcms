{extends file="default_list.tpl"}


{block name="init"}


	{function dl_cell_subject}
		{if $item->group_cnt}
			({$item->group_cnt+1})
		{/if}
		
		{$item->subject}
	{/function}
	{function dl_cell_sender}
		{$options.user_id[$item->sender]}
	{/function}	
	

	{function dl_cell_message}
			<a href="#show_msg" onclick="gwcms.open_iframe({ url:GW.ln+'/'+GW.path+'/{$item->id}/view', title:'{GW::l('/m/MESSAGES')}' }); return false">
				{$item->message|truncate:'60'}
			</a>		
	{/function}
	

	{$display_fields=[insert_time=>1,subject=>1,message=>1, sender=>1,update_time=>1]}
	{$dl_smart_fields=[subject,message,sender]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	

	{function dl_actions_invert_seen}
		{if $item->seen==0}{$tmp='fa fa-eye-slash'}{else}{$tmp='fa fa-eye'}{/if}
		{list_item_action_m url=[false, [act=>doInvertSeen,id=>$item->id]] iconclass=$tmp}		
	{/function}		
	
	{$dl_actions=[invert_seen,edit,delete]}
		
		
	{*$dl_order_enabled_fields=array_keys($display_fields)*}
	{$dl_filters=$display_fields}	
	
	
	
	{if $m->admin}
		{$do_toolbar_buttons[] = readall}
		{$do_toolbar_buttons[] = hidden}
		{$do_toolbar_buttons_hidden[]=dialogconf}	
		{$do_toolbar_buttons_hidden[]=print}	
	{else}
		{$do_toolbar_buttons = [readall]}
	{/if}	

	
	{function do_toolbar_buttons_readall}
		{toolbar_button href=$m->buildUri(false, [act=>doMarkasReadAll]) title=GW::l('/m/MARK_AS_READ_ALL') iconclass="gwico-Read-Message"}
	{/function}
	
{/block}