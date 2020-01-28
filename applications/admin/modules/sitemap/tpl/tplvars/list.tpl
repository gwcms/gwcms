{extends file="default_list.tpl"}

{block name="init"}

	{function dl_cell_type}
		{$m->lang.VAR_TYPE_OPT[$item->type]}	
	{/function}	
	{function dl_cell_params}
		{json_encode($item->params)}
	{/function}	
	
	{$do_toolbar_buttons = [addnew,hidden]}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,print]}
	{*dialogconf*}
	
	

	{$dl_smart_fields=[type,params]}
	
	
	{$dl_actions=[edit,ext_actions]}
{/block}