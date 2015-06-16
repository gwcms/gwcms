{extends file="default_list.tpl"}


{block name="after_list"}
	{*ant newsletter geresnis sprendimas*}
<script type="text/javascript">function addcredit(id, val){ alert(val) }</script>

{/block}

{block name="init"}


	{$display_fields=[title=>1,subject=>1,lang=>1,status=>1,recipients_count=>1,sent_count=>1,insert_time=>1,update_time=>1]}
	{$dl_smart_fields=[status]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[test,clone,edit,delete,send]}
	
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
		{if $item->status < 10}
			{gw_link do="send" icon="email_go" params=[id=>$item->id] show_title=0 confirm=1 title="Send email"}
		{else}
			{gw_link icon=action_info relative_path="sentinfo" params=[id=>$item->id] title=$m->lang.VIEWS.sentinfo show_title=0}
		{/if}
	{/function}
	
	{function dl_cell_status}
		{$m->lang.OPT.status[$item->status]}
		
		{if $item->sent_count}
			
		{/if}
	{/function}
	

		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}