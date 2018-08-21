{extends file="default_list.tpl"}


{block name="init"}

	
	{function name=do_toolbar_buttons_synchronizefromxml} 
		{toolbar_button title=GW::l('/A/VIEWS/synchronizefromxml') iconclass='gwico-Refresh' href=$m->buildUri(synchronizefromxml)}	

	{/function}	
	
	
	
	{function dl_cell_filename}
		{if $item->content_cat == 'file'}
			{$file=$item->file}
			{$file->original_filename}
		{else}
			{$file=$item->image}
			{$file->original_filename}
		{/if}

	{/function}
	


	{function name=dl_actions_preview}
		<a href="{$m->buildUri(false,[act=>doPreview,id=>$item->id, clean=>1])}" class="iframe-under-tr"><i class="fa fa-search"></i></span>		
	{/function}
	
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[synchronizefromxml,exportdata,importdata,dialogconf,print]}		
	
	
		
	{$dl_actions=[preview,edit,delete]}
	{$dl_smart_fields=[filename]}
	{$dl_inline_edit=1}		
	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}
	
	
{/block}


