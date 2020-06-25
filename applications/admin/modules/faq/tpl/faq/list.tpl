{extends file="default_list.tpl"}


{block name="init"}
	
	{function name=do_toolbar_buttons_groups} 
		{toolbar_button title="Grupės" href=$m->buildUri(groups,[clean=>2],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
	{/function}	
	
	

	{function name=dl_cell_image}
		{$image=$item->image}
		{if $image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size=50x50" align="absmiddle" vspace="2" title="{$item->title|escape}" />
		{/if}
	{/function}

	{function name=dl_cell_group_id}
		{$m->options.group_id[$item->group_id]}
	{/function}	
	

	{$dl_smart_fields=[image,group_id]}
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}	
	
	{$do_toolbar_buttons[] = dialogconf}	
	
	{*$do_toolbar_buttons[] = groups*}
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	
	
	
	{$dl_actions=[invert_active,move,edit,delete]}
	
	{$dl_filters=[title=>1, insert_time=>1, active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
	
	

{/block}