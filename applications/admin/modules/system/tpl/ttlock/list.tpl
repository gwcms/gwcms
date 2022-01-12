{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="test" href=$m->buildUri(tests)  iconclass="fa fa-download"}
	{/function}	
	

	
		
	{$do_toolbar_buttons_hidden=[modactions,dialogconf,dialogconf2]}
	
	
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	{$dl_actions=[delete]}

	{$dl_output_filters=[insert_time=>short_time]}	
		


	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	

	
	
	
	{$dl_smart_fields=[type,count]}
	
	
	
{/block}