{extends file="default_list.tpl"}

{block name="init"}

	{$icons=[0=>file,1=>folder,2=>link]}


	{function name=do_toolbar_buttons_importexport}
		{toolbar_button title=GW::l('/A/VIEWS/importexporttree') iconclass='gwico-Sorting-Arrows-Filled' href=$m->buildUri(importexporttree)}	
	{/function}
	{function name=do_toolbar_buttons_fixpaths}
		{toolbar_button title="<span class='text-muted'>{GW::l('/m/VIEWS/doFixPaths')}</span>" iconclass='gwico-Refresh' href=$m->buildUri(false,[act=>doFixPaths])}	
		
		{if $app->user->isRoot()}
			{toolbar_button title="<span class='text-muted'>{GW::l('/m/VIEWS/doAddExtLn')}</span>" iconclass='gwico-Upload-SVG' href=$m->buildUri(false,[act=>doAddExtLn])}	
		{/if}
	{/function}	
	
	
	{$do_toolbar_buttons[] = hidden}	
	{$do_toolbar_buttons_hidden=[dialogconf,importexport,print,fixpaths]}	
	

	{function dl_cell_ico}
		{if $item->type==4}
			<i class="fa fa-external-link" aria-hidden="true" style="margin:2px"></i>
		{elseif $item->type==2}
			<i class="fa fa-external-link-square" style="margin:2px"></i>
		{else}
			
		<img src="{$app->icon_root}{$icons[$item->type]}.png" align="absmiddle" vspace="2" />	
		{/if}
	{/function}
	{function dl_cell_icon}
		{if $item->icon}
			{if strpos($item->icon,'/')!==false}
				<img style="height:16px;background-color:silver" src="{$item->icon}" align="absmiddle" vspace="2" />	
			{elseif strpos($item->icon,' ')!==false} {*sumatchins jei bus pvz: fa fa-kazkas*} 
				<i class='{$item->icon}'></i>
			{/if}		
		{/if}
	{/function}	
	{function dl_cell_title}
		{if $item->type!=2}
			{gw_link params=[pid=>$id] title=$item->title}
		{else}
			{$item->title}
		{/if}
		
		{if $item->child_count}
			({$item->child_count})
		{/if}
	{/function}
	
	{function dl_cell_in_menu}
			{foreach GW::$settings.LANGS as $ln_code}
				{if $item->get("in_menu_`$ln_code`")}
					<a href="{$sys_base}{$ln_code}/{$item->path}" title="live view">{$ln_code|strtoupper}</a>
				{/if}
			{/foreach}		
	{/function}
	

	{$dl_smart_fields=[title,in_menu,ico,icon]}
	
	
	
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}	
	
	
	
	
	
	
	{if $m->filters.parent_id}
		{*tree display*}
		{$dl_actions=[invert_active,move,edit,ext_actions]}
	{else}
		{*one level list display*}
		{$dl_actions=[invert_active,edit,ext_actions]}
	{/if}
	
	{gw_unassign var=$display_fields.ico}

	
	{$dl_filters=$display_fields}
	{$dl_order_enabled_fields = []}
	
	
	
	{$dl_checklist_enabled=1}
	{function "dl_cl_actions_changeparent"}<option value="checked_action_postids('{$m->buildUri(false,[act=>doChangeParent])}', true)">Perkelti</option>{/function}
	{$dl_cl_actions=[invertactive,dialogremove,changeparent]}
	
{/block}
