{extends file="default_list.tpl"}


{block name="init"}

		

	{$do_toolbar_buttons[] = dialogconf}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}			
	
	{$do_toolbar_buttons[] = search}		
	
	{$dl_actions=[preview,edit,ext_actions]}
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	


	{function dl_actions_preview}
		{if $item->isdir==0}
			{list_item_action_m url=[showdecrypted,[id=>$item->id,clean=>1]] iconclass="fa fa-eye" action_addclass="iframe-under-tr"}
		{/if}
	{/function}	

{/block}