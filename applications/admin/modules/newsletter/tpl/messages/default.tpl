{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[title=>1, subject_lt=>1,insert_time=>1,update_time=>1]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[clone,edit,delete]}
	

		
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}