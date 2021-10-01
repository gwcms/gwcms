{extends file="default_list.tpl"}




{block name="init"}	
        
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,divider,print,divider,runtest]}   
	
	{function name=do_toolbar_buttons_runtest}  
		{toolbar_button title="Check and run" iconclass='gwico-cog' href=$m->buildUri(false, [act=>doCheckAndRun])}
	{/function}		
	

	{$dl_actions=[timematch,invert_active,editshift,delete,clone]}
	
		
	{$dl_smart_fields=[template_id,groups]}

	{function dl_cell_template_id}
		{$options.template_id[$item->template_id]->title}
	{/function}
	
	{function dl_cell_groups}
		
		{foreach $item->groups as $groupid}
			{$options.group_id[$groupid]->title}
		{/foreach}
		
	{/function}	
	
	{$dl_order_enabled_fields=$dl_fields}
	
	{function dl_actions_timematch}
		{list_item_action_m url=[false,[act=>doRun,id=>$item->id]] iconclass="fa fa-caret-square-o-right" title="Run!"}
		{list_item_action_m url=[false,[act=>doTestTimeMatch,id=>$item->id]] caption=TTM title="Test time match"}	
	{/function}	
	
	
	
{/block}
