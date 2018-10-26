{extends file="default_list.tpl"}


{block name="init"}

	{$dl_smart_fields=[status,hits,recipients_ids,recipients_count]}
	
	{$dl_filters=[]}	
	
	

	
	{$do_toolbar_buttons[] = addmultilang}	
	{$do_toolbar_buttons[] = hidden}	
	{$do_toolbar_buttons_hidden = [dialogconf,modinfo]}	
	
	{$do_toolbar_buttons[] = search}	
	
	
	
	{function name=do_toolbar_buttons_addmultilang} 
		{toolbar_button title=GW::l('/A/VIEWS/multilangform') iconclass='fa fa-plus-circle' 
			href=$m->buildUri('multilangform',[id=>0])}	
	{/function}		
	
	
	
	{$dl_actions=[test,clone,edit,invert_active,delete,send]}
	
	{function dl_actions_test}

		
	{list_item_action_m 
			url=[false,[act=>doTest,id=>$item->id,'mail'=>0]] 
			onclick="x=prompt('Test laiško gavėjas', '`$lasttestmail`'); ;if(!x)return false; location.href=this.href.replace('mail=0','mail='+x)"
			iconclass="fa fa-fw fa-eye" 
			tag_params=[target=>'_blank', title=>"Bus siunčiamas laiškas nurodytu el. pašto adresu"]}
	{/function}
	
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
	
	

		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}