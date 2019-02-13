{extends file="default_list.tpl"}


{block name="init"}

		
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		
		
	{$dl_inline_edit=1}		
	
	{$dl_checklist_enabled=1}
	
	{$dl_output_filters_truncate_size=100}
	
	{$dl_output_filters.body=truncate}
	{$dl_output_filters.status=truncate}
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}
	
	
	{$dl_actions=[edit,delete_ajax,send,preview]}	
	
	{function name=dl_actions_send}
		{if $item->status=="SENT"}
			{$tmp=1}{$color="text-warning"}
			{$tmp2=['data-confirm_text'=>GW::l('/m/REPEAT_SEND_CONFIRM')]}
		{else}
			{$tmp=0}{$color=""}
			{$tmp2=""}
		{/if}
		{list_item_action_m url=[false,[act=>dosend,id=>$item->id]] iconclass="fa fa-send-o `$color`" confirm=$tmp tag_params=$tmp2 action_addclass="ajax-link"}
	{/function}

	{function dl_actions_preview}
		{list_item_action_m url=[false,[act=>doViewBody,id=>$item->id]] iconclass="fa fa-eye" action_addclass="iframe-under-tr"}
	{/function}	
	
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}
	
	
{/block}


