{extends file="default_list.tpl"}


{block name="init"}

	{function name=do_toolbar_buttons_countryact} 				
		{toolbar_button 
			title="* Download country flags"
			href=$m->buildUri(false, [act=>doGetFlags])}
		
	{/function}	
	
	{function name=do_toolbar_buttons_actions}
		{call name="do_toolbar_buttons_dropdown" do_toolbar_buttons_drop=$do_toolbar_buttons_actions groupiconclass="fa fa-angle-down" grouptitle="Veiksmai"}
	{/function}		
	{$do_toolbar_buttons_actions[]=countryact}
	{$do_toolbar_buttons[]=actions}
	
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}			
	{$do_toolbar_buttons[] = search}	
	
	{$dl_actions=[edit,delete]}
	


{/block}