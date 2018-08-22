{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_fields=[id,size,update_time, new_size]}
	
	
	{function dl_cell_size}
		{GW_Math_Helper::cfilesize($item->size)} 
	{/function}
	
	{function dl_cell_new_size}
		{GW_Math_Helper::cfilesize($item->new_size)} 
	{/function}	
	
	
	{$dl_smart_fields=['size','new_size']}
	
	{$dl_actions=[ext_actions]}

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
