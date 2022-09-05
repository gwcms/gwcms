{extends file="default_list.tpl"}


{block name="init"}
	
	
	
	{function name=do_toolbar_buttons_modactions} 
		{*{toolbar_button title="Suskaiciuoto produktus" href=$m->buildUri(false,[act=>doCounts])  iconclass="fa fa-refresh"}*}
		{if $app->user->isRoot() && GW::s('PROJECT_ENVIRONMENT') != $smarty.const.GW_ENV_DEV}
			{toolbar_button title=doSendToDev iconclass='gwico-Export' href=$m->buildUri(false,[act=>doSendToDev])}	
		{/if}
	{/function}	
	
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,modactions]}
	
	{$do_toolbar_buttons[]=hidden}
	{$do_toolbar_buttons[]=search}

	
	
	
	
	{$dl_actions=[edit,delete,clone]}

	{$dl_output_filters=[
		value=>truncate,
		time=>short_time]}	
		

	{$dl_inline_edit=1}	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	

	
	
	
	{$dl_smart_fields=[type,count]}
	
{/block}