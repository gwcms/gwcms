{extends file="default_list.tpl"}


{block name="init"}
	
	
	
	{function name=do_toolbar_buttons_modbuttons} 
		{toolbar_button title=GW::l('/A/VIEWS/translatetest') iconclass='gwico-Settings' href=$m->buildUri(translatetest)}	
			
	{/function}	
		
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[modbuttons,dialogconf2]}	
	

	
	{$dl_fields=[id,size,update_time,newsize,newtime]}
	
	
	{function dl_cell_size}
		{GW_Math_Helper::cfilesize($item->size)} 
	{/function}
	{function dl_cell_newsize}
		{GW_Math_Helper::cfilesize($item->newsize)} 
	{/function}	
	
	
	{$dl_smart_fields=[size,newsize]}
	
	
	
	{function name=dl_actions_flatedit}
		<a href="{$m->buildUri(flatedit,[id=>$item->id])}"><i class="fa fa-pencil-square-o"></i></span>		
	{/function}	
	
	
	
	{$dl_actions=[flatedit,ext_actions]}

	{$dl_order_enabled_fields=[]}
	{$dl_output_filters=[insert_time=>short_time,update_time=>short_time,newtime=>short_time]}	
	
	
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
