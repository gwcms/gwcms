{extends file="default_list.tpl"}

{block name="init"}

	{$icons=[0=>file,1=>folder,2=>link]}



	{function dl_cell_ico}
		{if $item->type==4}
			<i class="fa fa-external-link" aria-hidden="true" style="margin:2px"></i>
		{elseif $item->type==2}
			<i class="fa fa-external-link-square" style="margin:2px"></i>
		{else}
			
		<img src="{$app->icon_root}{$icons[$item->type]}.png" align="absmiddle" vspace="2" />	
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
	
	{$display_fields = [
		ico=>1,
		path=>1,
		pathname=>0,
		title=>1,
		in_menu=>1,
		insert_time=>1,
		update_time=>1
	]}
	
	{$dl_fields=$m->getDisplayFields($display_fields)}	
	{$dl_smart_fields=[title,in_menu,ico]}
	
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}	
	
	{$do_toolbar_buttons[] = dialogconf}	
	
	
	{function dl_actions_preview}
		{list_item_action_m 
			url=[false,[act=>doPreview,id=>$item->id]] 
			iconclass="fa fa-external-link" 
			tag_params=[target=>'_blank', title=>GW::l('/m/VIEWS/doPreview')]}
	{/function}
	
	
	
	{if $m->filters.parent_id}
		{*tree display*}
		{$dl_actions=[invert_active,move,edit,delete,clone,preview]}
	{else}
		{*one level list display*}
		{$dl_actions=[invert_active,edit,delete,clone]}
	{/if}
	
	{gw_unassign var=$display_fields.ico}

	
	{$dl_filters=$display_fields}
	{$dl_order_enabled_fields = []}
{/block}
