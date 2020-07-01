{extends file="default_list.tpl"}


{block name="init"}

	
	{function name=do_toolbar_buttons_synchronizefromxml} 
		{toolbar_button title=GW::l('/A/VIEWS/synchronizefromxml') iconclass='gwico-Refresh' href=$m->buildUri(synchronizefromxml)}	

	{/function}	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[synchronizefromxml,exportdata,importdata,dialogconf,print]}		
	
	
		
	{$dl_actions=[edit,delete,clone]}
	
	{$dl_inline_edit=1}		
	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	
	
{/block}


