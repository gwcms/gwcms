{extends file="default_list.tpl"}

{block name="init"}
	


	{$dl_inline_edit=1}
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	
	
	{$dl_actions=[edit,clone,delete]}
	
	
	{$dl_output_filters=[
		insert_time=>short_time,
		update_time=>short_time
		]}
	
	{$dl_calc_totals.profit=0}
	
	
{/block}