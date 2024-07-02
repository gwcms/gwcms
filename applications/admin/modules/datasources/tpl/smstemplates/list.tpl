{extends file="default_list.tpl"}


{block name="init"}

	{if $m->admin}
		{$display_fields.user_id=1}
	{/if}	
	
	{$dl_smart_fields=[message,user_id,insert_time]}
	
	{function name=dl_cell_message}
		{$item->message|truncate:100}
	{/function}		
	{function name=dl_cell_user_id}
		{if $item->user_id}
			{$options.user_id[$item->user_id]}
		{else}-{/if}
	{/function}	
	{function name=dl_cell_insert_time}
		{$time=explode(' ',$item->insert_time)}
		{$time.0}
	{/function}	
	
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	

	
	{$do_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[edit]}
	{if $m->write_permission}
		{$dl_actions[]=messagetpl}
		{$dl_actions[]=delete}
	{/if}
        
        {function dl_actions_messagetpl}
		{list_item_action_m url=[false, [act=>doSend,id=>$item->id]] iconclass="fa fa-envelope-o" title="Siųsti"}
        {/function}
	
	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}    
	
	
{/block}