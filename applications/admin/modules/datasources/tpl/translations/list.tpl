{extends file="default_list.tpl"}


{block name="init"}

	
	{function name=do_toolbar_buttons_synchronizefromxml} 
		{toolbar_button title=GW::l('/A/VIEWS/synchronizefromxml') iconclass='gwico-Refresh' href=$m->buildUri(synchronizefromxml)}	

	{/function}	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[synchronizefromxml,exportdata,importdata,dialogconf,print]}		
	
	
	
	
		
	{$dl_actions=[edit,clone,delete,ext_actions]}
	
	{$dl_inline_edit=1}	


	{$dl_output_filters=[]}
	
	{$dl_output_filters_truncate_size=100}
	
	{foreach GW::$settings.LANGS as $lncode}
		{$dl_output_filters["value_`$lncode`"]=truncate}
	{/foreach}	
	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}
	
	
{/block}


