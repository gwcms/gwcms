{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="test" href=$m->buildUri(tests)  iconclass="fa fa-download"}
		{toolbar_button title="doRemoveRemoteOld" href=$m->buildUri(false, [act=>doRemoveRemoteOld])  iconclass="fa fa-remove"}
		
	{/function}	
	

	
		
	{$do_toolbar_buttons_hidden=[modactions,dialogconf,dialogconf2]}
	
	
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	{$dl_actions=[ext_actions]}

	{$dl_output_filters=[insert_time=>short_time]}	
		


	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	

	
	
	
	{$dl_smart_fields=[type,count]}
	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doRemoteDelete])}',1)">{GW::l('/A/VIEWS/doRemoteDelete')}</option>{/capture}
	{capture append="dl_checklist_actions"}<option value="checked_action('{$m->buildUri(false,[act=>doSeriesAct,action=>doRemoteCreate])}',1)">{GW::l('/A/VIEWS/doRemoteCreate')}</option>{/capture}
	
	
{/block}