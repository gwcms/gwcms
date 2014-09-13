{extends file="default_list.tpl"}

{block name="init"}

	{*function dl_actions_switchtouser}
		{gw_link do="switch_user" icon="switch_user" params=[id=>$item->id] show_title=0}
	{/function*}
	
	{$display_fields = [
		id=>1,
		username=>1,
		name=>1,
                email=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{function dl_cell_name}
                {$item->first_name} {$item->second_name}
	{/function}
	
	{$dl_smart_fields=[name]}
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
{/block}
