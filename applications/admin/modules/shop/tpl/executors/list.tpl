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
	{$dl_smart_fields = [shipprices,execprices]}
	
	{function dl_cell_execprices}
		{$cnt=count($item->execprice)}

		<a title="Exec prices {$item->items_count}" class='gwcmsAction iframe-under-tr' href="{$m->buildUri("`$item->id`/execprice",[clean=>2])}">
			<i class="fa fa-money"></i> <span style='color:violet;position:relative;left:-4px'>{$cnt}</span>
		</a>	
		
	{/function}
	{function dl_cell_shipprices}
		{$cnt=count($item->shipprice)}
		<a title="Ship Price {$item->items_count}" class='gwcmsAction iframe-under-tr' href="{$m->buildUri("`$item->id`/shipprice",[clean=>2])}">
			<i class="fa fa-ship"></i> <span style='color:orange;position:relative;left:-4px'>{$cnt}</span>
		</a>	
		
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
