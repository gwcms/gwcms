{extends file="default_list.tpl"}

{block name="init"}

	
	
	{$dl_inline_edit=1}


	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	

	
	{function dl_cell_ico}

		
		{if $item->isdir==1}
			<i class="fa fa-folder-o"></i>
		{else}
			<i class="{Mime_Type_Helper::icon($item->path)}"></i>
		{/if}
		
		
		
	{/function}
	
	
	{function dl_actions_preview}
		{if $item->isdir==0}
			{list_item_action_m url=[preview,[id=>$item->id]] iconclass="fa fa-eye" action_addclass="iframe-under-tr"}
		{/if}
	{/function}		
	
	
	{$dl_smart_fields=[ico]}
	
	{$dl_actions=[preview,edit,delete,ext_actions]}
	

	
{/block}

