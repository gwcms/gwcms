{extends file="default_list.tpl"}




{block name="init"}	
	{$dl_fields=$m->getDisplayFields([
            title=>1,
            name=>1,
            time_match=>1,
            separate_process=>1,
            insert_time=>0,
            update_time=>0
        ])}
        
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,divider,print]}        

	{$dl_actions=[timematch,invert_active,edit,delete,clone]}
	
	{$dl_filters=[title=>1, insert_time=>1,active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
		
	{$dl_order_enabled_fields=$dl_fields}
	
	{function dl_actions_timematch}
		{list_item_action_m url=[false,[act=>doRun]] iconclass="fa fa-caret-square-o-right" title="Run!"}
		{list_item_action_m url=[false,[act=>doTestTimeMatch,id=>$item->id]] caption=TTM title="Test time match"}	
	{/function}	
	
{/block}
