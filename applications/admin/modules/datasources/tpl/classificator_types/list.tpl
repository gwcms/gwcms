{extends file="default_list.tpl"}


{block name="init"}
	
	
	{function name=do_toolbar_buttons_modactions} 
		{toolbar_button title="Suskaiciuoto produktus" href=$m->buildUri(false,[act=>doCounts])  iconclass="fa fa-refresh"}
	{/function}	
	
	{$do_toolbar_buttons_hidden=[modactions]}
	{$do_toolbar_buttons[]=dialogconf}
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	
	
	
	
	{$dl_actions=[edit,ext_actions]}

	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}
	
		
	{function dl_cell_count}
		{$url=$m->buildUri("`$item->id`/classificators",[clean=>2])}
		{*iconclass="fa fa-globe"*}
		{list_item_action_m href=$url 
			action_addclass="iframe-under-tr" caption="Items({$item->count})" 
			tag_params=["data-iframeopt"=>'{ "min-width":"1000px" }']}
	{/function}			
		
	{function dl_cell_type}
	
		{$m->lang.OPTIONS.classificator_types[$item->type]}
	{/function}
	
	
	
	{$dl_smart_fields=[type,count]}
	
	
	
{/block}