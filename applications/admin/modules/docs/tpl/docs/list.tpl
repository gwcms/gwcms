{extends file="default_list.tpl"}


{block name="init"}

	
	{$dl_smart_fields=[recipients_total]}	
	

	
	{$do_toolbar_buttons[] = hidden}
	
	{$do_toolbar_buttons_hidden=[dialogconf,dialogconf2,print,testpdfgen,exportdata,importdata]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[edit,ext_actions]}
	
	{function name=do_toolbar_buttons_testpdfgen}
		{toolbar_button title=GW::l('/A/VIEWS/testpdfgen') iconclass='fa fa-file-pdf-o' href=$m->buildUri(testpdfgen)}	
	{/function}


	
{/block}