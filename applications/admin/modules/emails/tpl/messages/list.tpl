{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[title=>1,sender=>1,subject=>1,lang=>1,status=>1,recipients_count=>1,sent_count=>1,hits=>1,insert_time=>1,update_time=>1,sent_time=>0]}
	{$dl_smart_fields=[status,hits]}
	
	{$dl_filters=[
		title=>1,subject=>1,sender=>1,insert_time=>1,sent_time=>1,
		lang=>[type=>select, options=>$m->lang.OPT.lang],
		status=>[type=>select, options=>$m->lang.OPT.status]
	]}	
	
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$do_toolbar_buttons[] = dialogconf}	
	
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
				{list_item_action_m url=[send,[id=>$item->id]] confirm=1 iconclass="fa fa-send" title="Send email"} 
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
	

		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}