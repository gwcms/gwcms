{extends file="default_list.tpl"}



{block name="init"}


	{$dl_filters=[]}
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[invert_active_ajax,edit,move,ext_actions]}
	{$dl_smart_fields=[options_src]}
	
	{$dl_output_filters=[
		title_lt=>expand_truncate,
		title_en=>expand_truncate,
		title_ru=>expand_truncate
	]}		

	{function dl_cell_options_src}
		{$id=$item->options_src}
		{if $id}
			{$url=$app->buildUri("datasources/cassificator_types/`$id`/classificators",[clean=>2])}
			{*iconclass="fa fa-globe"*}
			{$title=$item->optionsgroup->title}
			{list_item_action_m href=$url 
				action_addclass="iframe-under-tr" caption="{$title}({$classificator_type_cnt[$id]})" 
				tag_params=["data-iframeopt"=>'{ "min-width":"1000px" }']}
			{/if}
	{/function}	

{/block}

