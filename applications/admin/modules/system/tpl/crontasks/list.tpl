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
        
	{$dl_toolbar_buttons[] = hidden}
	{$dl_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}        

	{$dl_actions=[timematch,invert_active,edit,delete,clone]}
	
	{$dl_filters=[title=>1, insert_time=>1,active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
		
	{$dl_order_enabled_fields=$dl_fields}
	
	{function dl_actions_timematch}
		{gw_link do=test_time_match params=[id=>$item->id] title="timeTest"}
		{gw_link do=run params=[id=>$item->id] title="Run!"}
	{/function}	
	
{/block}
