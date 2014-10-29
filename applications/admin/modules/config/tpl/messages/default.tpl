{extends file="default_list.tpl"}


{block name="init"}


	{function dl_cell_subject}
		{if $item->group_cnt}
			({$item->group_cnt+1})
		{/if}
		
		{$item->subject}
	{/function}

	{function dl_cell_message}
		{if $smarty.request.read_all}
			<pre class="gw_pre">{$item->message}</pre>
			{if !$item->seen}{$success=$item->saveValues([seen=>1])}{/if}
		{else}
			<a href="#show_msg" onclick="open_ajax({ url:GW.ln+'/'+GW.path+'/{$item->id}/view', title:this.innerHTML }); return false">
				{$item->message|truncate:'60'}
			</a>		
		{/if}
	{/function}

	{$display_fields=[insert_time=>1,subject=>1,message=>1, sender=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_smart_fields=[subject, message]}
	
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[invert_active,edit,delete]}
		
		
	{*$dl_order_enabled_fields=array_keys($display_fields)*}
	{$dl_filters=$display_fields}	
	
	
	{$dl_toolbar_buttons[]=read_all}

	
	{function dl_toolbar_buttons_read_all}
		{$readAll=!$smarty.request.read_all}
		{gw_link title=$m->lang.READ_ALL params=[read_all=>$readAll]}
	{/function}
	
{/block}