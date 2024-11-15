{extends file="default_list.tpl"}

{block name="init"}
	{$dl_fields=[title,insert_time]}
	
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}	
	
	{$dl_actions=[invert_active,edit,delete]}
	{$dl_filters=[]}
	{$do_toolbar_buttons[] = search}
	
{/block}
