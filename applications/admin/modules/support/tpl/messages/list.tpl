{extends file="default_list.tpl"}


{block name="init"}


	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		

	{$dl_actions=[edit,delete]}

	{$dl_filters=$display_fields}
	{$dl_smart_fields=[insert_time]}
	
	{function dl_cell_insert_time}
		{$x=explode(' ',$item->insert_time)}
		<span title="{$x.1}">{$x.0}</span>
	{/function}	


{/block}