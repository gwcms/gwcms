{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_fields=[id,size,update_time, new_size]}
	
	
	{function dl_cell_size}
		{GW_Math_Helper::cfilesize($item->size)} 
	{/function}
	
	{function dl_cell_new_size}
		{GW_Math_Helper::cfilesize($item->new_size)} 
	{/function}	
	
	{function dl_actions_logw}
		{gw_link relative_path="newlines" params=[id=>$item->id] title="NewLines"}
		{gw_link relative_path="realtime" params=[id=>$item->id] title="RealTime"}
		
		<a href="#" onclick="open_rtlogview('{$item->id}'); return false">Realtime D</a>

			
		{gw_link relative_path="entire" params=[id=>$item->id] title="Entire"}	
	{/function}
	
	{$dl_smart_fields=['size','new_size']}
	
	{$dl_actions=[logw]}

	{$dl_order_enabled_fields=[]}
{/block}
