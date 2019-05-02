{extends file="default_list.tpl"}
{block name="init"}

	{$dl_inline_edit=1}
	
	{function name=do_toolbar_buttons_types} 
		{toolbar_button title="Tipai 1" href=$m->buildUri(types,[clean=>2,type=>'grouptype1'],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
		{toolbar_button title="Tipai 2" href=$m->buildUri(types,[clean=>2,type=>'grouptype2'],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
		{toolbar_button title="Adresai" href=$m->buildUri(locations,[clean=>2],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
	{/function}	

	
	
	
	{$do_toolbar_buttons[] = hidden}
	
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	
	{$do_toolbar_buttons_hidden[] = types}
	
	
	{function dl_cell_description}
		{if mb_strlen($item->description)>50}
			<span title="{$item->description|escape}">{$item->description|escape|truncate:50}</span>
		{else}
			{$item->description}
		{/if}
		
	{/function}
	{function name=dl_cell_title}
		{if $item->title}
			<a href="{$app->buildUri("scheduler/groups/`$item->id`/sched",[group_id=>$item->id])}">{$item->title}</a>
		{else}
			-
		{/if}
	{/function}

	{function dl_actions_parts}

				
		{*iconclass="fa fa-globe"*}
		{list_item_action_m url=["`$item->id`/sched",[clean=>2,group_id=>$item->id]] action_addclass="iframe-under-tr" title="Dalys" caption="Dalys({$item->slot_count})"}				
				
		<a class="gwcmsAction" href="{$m->buildUri("`$item->id`/sched",[group_id=>$item->id])}"><span><i class="fa fa-external-link"></i></span></a>
	{/function}	
	

	{$dl_smart_fields=[address,title,type1,type2,participant_list_id]}

	{function dl_cell_type1}
		{$options.type1[$item->type1]->title} ({$options.type1[$item->type1]->key})
	{/function}
	
	{function dl_cell_type2}
		{$options.type2[$item->type2]->title} ({$options.type2[$item->type2]->key})
	{/function}
	
	{function dl_cell_address}
		{if strlen($item->description) > 50}
			<span title="{$item->address|escape}">{$item->address|truncate:50}</span>
		{else}
			{$item->address}
		{/if}
	{/function}

	{function dl_cell_participant_list_id}
		{$listobj=$item->participantList()}
		{if $listobj}
			{$listobj->title}
		{/if}
	{/function}	

	{$dl_actions=[move,invert_active,edit,parts,ext_actions]}
	
	{function name=dl_output_filters_truncate40_hint}
		{call name="truncate_hint" value=htmlspecialchars($item->$field) length=40}
	{/function}		
	
	
	{$dl_output_filters=[
		header_text_lt=>truncate40_hint,
		header_text_en=>truncate40_hint,
		header_text_ru=>truncate40_hint,
		description_lt=>truncate40_hint,
		description_en=>truncate40_hint,
		description_ru=>truncate40_hint,
		table_description_lt=>truncate40_hint,
		table_description_en=>truncate40_hint,
		table_description_ru=>truncate40_hint
	]}		
	
	
{/block}

