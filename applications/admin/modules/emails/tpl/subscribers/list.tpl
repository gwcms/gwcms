{extends file="default_list.tpl"}


{block name="init"}

	{function name=do_toolbar_buttons_emailsfromtext}  
		{toolbar_button title=GW::l('/A/VIEWS/emailsfromtext') iconclass='gwico-Import' href=$m->buildUri(emailsfromtext)}
	{/function}	
	
	{$dl_smart_fields=[title,email,groups,confirmed]}
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[importdata,exportdata,dialogconf,emailsfromtext,print]}	
	{$do_toolbar_buttons[] = search}
	
	
	{$dl_actions=[invert_active,edit,delete]}
	{$dl_output_filters=[update_time=>short_time,insert_time=>short_time]}	
	

	

	{function dl_cell_title}
		{if $item->unsubscribed}<s style="color:gray">{$item->title}</s>{else}{$item->title}{/if}
	{/function}
	{function dl_cell_email}
		{if $item->unsubscribed}<s style="color:gray">{$item->email}</s>{else}{$item->email}{/if}
	{/function}

	{function dl_cell_groups}
		{foreach from=$item->groups key=ind item=gid}
			<a href="{$app->ln}/{$app->page->path}/groups?id={$gid}" title="{GW::l('/g/EDIT')}">{$options.groups.$gid}</a>
		{/foreach}	
	{/function}
	
	
	{function dl_cell_confirmed}
		{if $item->confirm_code==0}
			-
		{elseif $item->confirm_code==7}
			{GW::l('/g/YES')}
		{else}
			{GW::l('/g/NO')}, {GW::l('/m/CONFIRM_CODE')} {$item->confirm_code}
		{/if}
	{/function}	
	
		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}	
	
{/block}