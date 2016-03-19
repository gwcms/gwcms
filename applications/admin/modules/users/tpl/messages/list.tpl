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
			<a href="#show_msg" onclick="open_ajax({ url:GW.ln+'/'+GW.path+'/{$item->id}/view', title:'{$m->lang.MESSAGES}' }); return false">
				{$item->message|truncate:'60'}
			</a>		
	{/function}
	

	{$display_fields=[insert_time=>1,subject=>1,message=>1, sender=>1,update_time=>1]}
	{$dl_smart_fields=[subject,message,sender]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	

	
	
	{function dl_actions_invert_seen}
		{if $item->seen==0}{$color='orange'}{else}{$color='white'}{/if}
		
			{gw_link do=invert_seen params=[id=>$item->id] icon="dot_`$color`" title="Mark as read" show_title=0}
		
	{/function}		
	
	{$dl_actions=[invert_seen,edit,delete]}
		
		
	{*$dl_order_enabled_fields=array_keys($display_fields)*}
	{$dl_filters=$display_fields}	
	
	
	
	{if $m->admin}
		{$dl_toolbar_buttons[] = readall}
		{$dl_toolbar_buttons[] = hidden}
		{$dl_toolbar_buttons_hidden[]=dialogconf}	
		{$dl_toolbar_buttons_hidden[]=print}	
	{else}
		{$dl_toolbar_buttons = [readall]}
	{/if}	

	
	{function dl_toolbar_buttons_readall}
		{gw_link do=markasreadall title=$m->lang.MARK_AS_READ_ALL icon="mark_as_read_24"}
	{/function}
	
{/block}