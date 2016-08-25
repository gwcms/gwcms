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
		
		{list_item_action_m url=["newlines",[id=>$item->id]] title=NewLines caption=NL}
		{list_item_action_m url=["realtime",[id=>$item->id]] title=RealTime caption=RT}
				
		
		{list_item_action onclick="gwcms.open_rtlogview('`$item->id`');" caption=RTM title="Realtime in modal window"}
		
		{list_item_action_m url=["entire",[id=>$item->id]] caption=Ent title="Show Entire file"}	
		{list_item_action_m url=[false,[act=>doClean,id=>$item->id]] caption=Cl title=Clean}		
	{/function}
	
	{$dl_smart_fields=['size','new_size']}
	
	{$dl_actions=[logw]}

	{$dl_order_enabled_fields=[]}
	
	
	{capture append="footer_hidden"}
		<script>
		//testing
		/*
		$(function(){
			gwcms.open_rtlogview('system.log');
		})
		*/
		</script>

	{/capture}
{/block}
