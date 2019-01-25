{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_smart_fields=[recipients_total]}	
	

	
	{$do_toolbar_buttons[] = hidden}
	
	{$do_toolbar_buttons_hidden=[dialogconf,print,testpdfgen]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[edit,deleteCheck,clone,ext_actions]}
	
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
	
{/block}