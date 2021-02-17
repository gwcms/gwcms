{extends file="default_list.tpl"}
{block name="init"}

	{$dl_inline_edit=1}
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}

	
	{$dl_smart_fields=[admin_id]}
	{$dl_output_filters=[
		admin_note=>expand_truncate
	]}
			
	
	{$dl_output_filters.insert_time=short_time}
	{$dl_output_filters.update_time=short_time}		
	{$dl_output_filters.expires=short_time}		
	{$dl_output_filters.paytime=short_time}		
	
	
		
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = hidden}
	
	{$dlgCfg2MWdth=300}
	{$do_toolbar_buttons[] = dialogconf2}	
	
	{$do_toolbar_buttons_hidden=[]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[invert_active_ajax,edit,ext_actions]}
	
	{function name=dl_cell_admin_id}
		{$options.admin_id[$item->admin_id]->username}
	{/function}	

	{function name=dl_prepare_item}
		{if $item->pay_test=='1'}
			{$item->set('row_class', 'payment_test')}
		{/if}
		{if $item->status=='7'}
			{$item->set('row_class', "{$item->row_class} payment_payd")}
		{/if}
	{/function}	
		
	
	{*
	custom toolbar button:
	{function name=do_toolbar_buttons_exportphotos}
		{toolbar_button title=GW::l('/A/VIEWS/doExportPhotos') iconclass='gwico-Export' href=$m->buildUri(false,[act=>doExportPhotos])}	
	{/function}	
	
	
	from options:
	
	{function name=dl_cell_anyfieldname}
		{$m->options.anyfieldname[$item->anyfieldname]}
	{/function}	
	
	
	smart cell image:
	{function name="dl_cell_printprofilefotoE"}
	
		{$image=$user->image}
		
		{if $image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size=50x50&method=crop" 
				align="absmiddle" vspace="2" title="{$item->title|escape}"
			     />
		{/if}
	{/function}	
	*}
	
	
	{capture append="footer_hidden"}
	<style>
		.payment_payd { background-color: #cfc; }	
		.payment_test { color: brown; }
	</style>
	{/capture}	
{/block}
