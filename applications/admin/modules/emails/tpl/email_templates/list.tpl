{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_smart_fields=[recipients_total]}	
	

	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = search}	
	
	{$dl_actions=[edit,delete,clone]}
	
	

{/block}