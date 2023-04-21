{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_smart_fields=[recipients_total]}	
	
	{$dl_output_filters=[
		description_lt=>expand_truncate,
		description_en=>expand_truncate,
		description_ru=>expand_truncate
	]}	
	
	{$do_toolbar_buttons[] = hidden}
	
	{$do_toolbar_buttons_hidden=[dialogconf,print,testpdfgen,exportdata,importdata]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[edit,deleteCheck,clone,elements,answers,ext_actions]}
	
	{function name=do_toolbar_buttons_testpdfgen}
		{toolbar_button title=GW::l('/A/VIEWS/testpdfgen') iconclass='fa fa-file-pdf-o' href=$m->buildUri(testpdfgen)}	
	{/function}

	{function name=dl_actions_deleteCheck}
		{if $item->protected}
			<i class="fa fa-lock text-muted"></i>
		{else}
			{list_item_action_m url=[false,[act=>doDelete,id=>$item->id]] iconclass="fa fa-trash-o text-danger" confirm=1}
		{/if}
	{/function}	
	
	{function dl_actions_elements}
		{$url=$m->buildUri("`$item->id`/elements",[clean=>2])}
		{*iconclass="fa fa-globe"*}
		{list_item_action_m href=$url 
			action_addclass="iframe-under-tr" caption="Įvestys({$item->element_count})" 
			tag_params=["data-iframeopt"=>'{ "min-width":"1300px" }']}
	{/function}		
	
	{function dl_actions_answers}
		{$url=$m->buildUri("`$item->id`/answers",[clean=>2])}
		{*iconclass="fa fa-globe"*}
		{list_item_action_m href=$url 
			action_addclass="iframe-under-tr" caption="Atsakymai({$item->answer_count})"
			tag_params=['data-iframeopt'=>'{"min-width":"1000px","height":"600px"}']
		}
	{/function}		
	
{/block}