{extends file="default_list.tpl"}


{block name="init"}
	{$dl_fields=[name,insert_time,update_time]}
	{$dl_actions=[run,invert_active,edit,delete]}
	
	{function dl_actions_run}
		{gw_link do=executeQuery params=[id=>$item->id] title="Run!"}
	{/function}	
	
{/block}



