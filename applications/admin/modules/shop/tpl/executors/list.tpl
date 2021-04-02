{extends file="default_list.tpl"}
{block name="init"}

	{$dl_inline_edit=1}
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}

	
	{$dl_smart_fields=[]}
	{$dl_output_filters=[]}	
		
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[invert_active_ajax,edit,ext_actions]}
	
	
	
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
	
{/block}
