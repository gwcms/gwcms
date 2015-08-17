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
		{if $smarty.request.read_all}
			<pre class="gw_pre">{$item->message}</pre>
			{if !$item->seen}{$success=$item->saveValues([seen=>1])}{/if}
		{else}
			<a href="#show_msg" onclick="open_ajax({ url:GW.ln+'/'+GW.path+'/{$item->id}/view', title:'{$m->lang.MESSAGES}' }); return false">
				{$item->message|truncate:'60'}
			</a>		
		{/if}
	{/function}
	

	{$display_fields=[insert_time=>1,subject=>1,message=>1, sender=>1,update_time=>1]}
	{$dl_smart_fields=[subject,message,sender]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	
	{$dl_toolbar_buttons[] = dialogconf}	
	
	
	{function dl_actions_invert_seen}
		{if $item->seen==0}{$color='orange'}{else}{$color='white'}{/if}
		
			{gw_link do=invert_seen params=[id=>$item->id] icon="dot_`$color`" title="Mark as read" show_title=0}
		
	{/function}		
	
	{$dl_actions=[invert_seen,edit,delete]}
		
		
	{*$dl_order_enabled_fields=array_keys($display_fields)*}
	{$dl_filters=$display_fields}	
	
	
	{$dl_toolbar_buttons[]=read_all}

	
	{function dl_toolbar_buttons_read_all}
		{$readAll=!$smarty.request.read_all}
		{gw_link title=$m->lang.READ_ALL params=[read_all=>$readAll]}
	{/function}
	
{/block}