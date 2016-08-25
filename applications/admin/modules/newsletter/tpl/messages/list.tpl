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
		{gw_link 
			do="test" 
			icon="test" 
			title="testuoti"
			params=[id=>$item->id,'mail'=>0] show_title=0 
			tag_params=[onclick=>"x=prompt('Test laiško gavėjas', '`$lasttestmail`'); if(!x)return false; this.href=this.href.replace('mail=0','mail='+x)"]
		}
	{/function}
	
	{function dl_actions_send}
		{if $item->status < 11}
			{if $item->active}{gw_link relative_path="`$item->id`/send" icon="email_go" params=[id=>$item->id] show_title=0 confirm=1 title="Send email"}{/if}
		{else}
			{gw_link icon=action_info relative_path="`$item->id`/sentinfo" title=$m->lang.VIEWS.sentinfo show_title=0}
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