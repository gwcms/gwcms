{extends file="default_list.tpl"}


{block name="init"}

	{function name=do_toolbar_buttons_modact} 		
		{*
		{toolbar_button 
			title="Add system language"
			href=$m->buildUri(false, [act=>doGetFlags])}
		*}
	{/function}	
	
		
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,modact]}	
	{*
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}			
	*}
	{$do_toolbar_buttons[] = search}	
	
	{$dl_actions=[edit,delete]}
	


{/block}