{extends file="default_list.tpl"}



{block name="init"}



	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[]}
	{$dl_filters=[]}
	{$dl_smart_fields=[]}
	
	
	{$dl_actions=[edit,ext_actions]}
	{$dl_inline_edit=1}
	
	
	
	
{/block}

