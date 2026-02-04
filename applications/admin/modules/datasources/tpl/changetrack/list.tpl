{extends file="default_list.tpl"}


{block name="init"}

		
	
	
	{function dl_cell_old}
		{*GW_Data_to_Html_Table_Helper::doTableSingleRecord($item->old)*}
		{call "dl_output_filters_expand_truncate" val=json_encode($item->old) expand_truncate_size=60}
	{/function}
	
	{function dl_cell_new}
		{call "dl_output_filters_expand_truncate" val=json_encode($item->new) expand_truncate_size=60}
		{*GW_Data_to_Html_Table_Helper::doTableSingleRecord($item->new)*}
	{/function}



	
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		
	
	
		
	{$dl_actions=[delete]}
	{$dl_smart_fields=[old,new]}
	
	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	
	
	{if $m->filters}
		{$do_toolbar_buttons=[]}
		{$dl_filters=[]}
	{/if}	
	
{/block}


