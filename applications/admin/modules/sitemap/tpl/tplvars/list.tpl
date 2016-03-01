{extends file="default_list.tpl"}

{block name="init"}

	{function dl_cell_type}
		{$m->lang.VAR_TYPE_OPT[$item->type]}	
	{/function}	
	
	
	{$dl_toolbar_buttons = [addnew,hidden]}
	{$dl_toolbar_buttons_hidden=[exportdata,importdata,print]}
	{*dialogconf*}
	
	
	{$display_fields = [
		id=>0,
		title=>1,
		name=>1,
		type=>1,
		insert_time=>0,
		update_time=>0
	]}
	
	{$dl_smart_fields=[type]}
	
	
	{foreach $display_fields as $key => $value}
		{if $value}
			{$dl_fields[]=$key}
		{/if}
	{/foreach}
	
	{$dl_actions=[edit,delete]}
{/block}