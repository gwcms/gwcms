{extends file="default_list.tpl"}
{block name="init"}

	{$dl_inline_edit=1}
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}

	
	{$dl_smart_fields=[relations,contents]}
	{$dl_output_filters=[site_id=>options,insert_time=>short_time,update_time=>short_time]}	

	
	
		
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[]}	
	{*,ext_actions*}
	{$dl_actions=[invert_active_ajax,delete,edit,clone]}
	



	{function name=dl_cell_relations}
		
		<a class='badge bg-bro' title='' href="{$app->buildUri("sitemap/pages",[site_id=>$item->id])}">{$item->relations.sitemap}</a>

		{*<a class="iframeopen compositions badge bg-blu" href=''>
			{$item->rel_compositions}</a>
		*}
	{/function}	
	{function name=dl_cell_contents}
		
		{if $item->contents_type==3 || $item->contents_type==4}
			<span class="text-muted">[code]</span> <i>({GW_File_Helper::cFileSize(strlen($item->contents))})</i>
		{else}
			{$item->contents|escape}
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
	
{/block}
