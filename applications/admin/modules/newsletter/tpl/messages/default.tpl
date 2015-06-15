{extends file="default_list.tpl"}


{block name="after_list"}
	{*ant newsletter geresnis sprendimas*}
<script type="text/javascript">function addcredit(id, val){ alert(val) }</script>

{/block}

{block name="init"}


	{$display_fields=[title=>1,subject=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[test,clone,edit,delete]}
	
	{function dl_actions_test}
		{gw_link 
			do="test" 
			icon="test" 
			params=[id=>$item->id,'mail'=>0] show_title=0 
			tag_params=[onclick=>"x=prompt('Test laiško gavėjas', '`$lasttestmail`'); if(!x)return false; this.href=this.href.replace('mail=0','mail='+x)"]
		}
	{/function}
	

		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}