{extends file="default_list.tpl"}



{extends file="default_list.tpl"}


{block name="init"}

	
	{function name=dl_toolbar_buttons_synchronizefromxml} 
		{gw_link relative_path=synchronizefromxml title=GW::l('/m/VIEWS/synchronizefromxml') icon="action_action"} &nbsp;&nbsp;&nbsp; 
	{/function}	
	
	{$dl_toolbar_buttons[] = hidden}
	{$dl_toolbar_buttons_hidden=[synchronizefromxml,dialogconf]}		
	
	
	{$display_fields=[module=>1,key=>1]}

	{foreach GW::$settings.LANGS as $lncode}
		{$display_fields["value_$lncode"]=1}
	{/foreach}	
	
	{$dl_filters=$display_fields}
	
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_actions=[edit,delete]}
	
	
	
	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}


