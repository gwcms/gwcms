{extends file="default_list.tpl"}


{block name="init"}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	
	{function name=do_toolbar_buttons_config} 
		{toolbar_button title=GW::l('/A/VIEWS/config') iconclass='gwico-Vertical-Settings-Mixer' href=$m->buildUri(config)}
	{/function}	
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,dialogconf2]}		
	
	{$dl_actions=['edit','send']}
	
	
	{function name=dl_actions_send}
		{if $item->status==7}
			{$tmp=1}{$color="text-warning"}
			{$tmp2=['data-confirm_text'=>GW::l('/m/REPEAT_SEND_CONFIRM')]}
		{else}
			{$tmp=0}{$color=""}
			{$tmp2=""}
		{/if}
		{list_item_action_m url=[false,[act=>dosend,id=>$item->id]] iconclass="fa fa-send-o `$color`" confirm=$tmp tag_params=$tmp2 action_addclass="ajax-link"}
	{/function}	
	
	
	{$dl_output_filters=[
		msg=>expand_truncate,
		err=>expand_truncate,
		insert_time=>short_time, 
		update_time=>short_time]}
		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}	
	
	
	{$dl_calc_totals.weight=0}
{/block}


{block name="after_list"}
	<br />
	<small style="color:silver" >Last send info: {$m->config->last_send_info}</small>
{/block}