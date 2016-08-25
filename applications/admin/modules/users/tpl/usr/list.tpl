{extends file="default_list.tpl"}

{block name="init"}

	
	{$dl_checklist_enabled=1}
	{capture append="dl_checklist_actions"}<option value="checked_action('dialogremove')">{GW::l('/A/VIEWS/dialogremove')}</option>{/capture}
	
	

	{if $m->rootadmin}
		{$display_fields.parent_user_id=1}
	{/if}
	
	{$dl_smart_fields=[group_ids,name,online,parent_user_id,image]}
	
	
	{function dl_cell_group_ids}
		{foreach from=$item->group_ids key=ind item=gid}{if 
			$ind!=0}, {/if}<a 
				href="{$app->ln}/{$app->page->path}/groups?id={$gid}" 
				title="{$lang.EDIT}">{$options.group_ids.$gid}</a>{/foreach}	
	{/function}

	{function dl_cell_name}
		{$item->name} {$item->surname}
	{/function}
	
	{function dl_cell_parent_user_id}
		<a href="{$m->buildUri("`$item->parent_user_id`/form",[id=>$item->parent_user_id])}" title="{$item->parent_user_title}">{$item->parent_user_title|truncate:10}</a>
	{/function}
	
	
	{function dl_cell_online}
		<img src="{$app->icon_root}{if $item->online}dot_green{else}dot_white{/if}.png">
	{/function}
	
	{function dl_cell_image}

		{$im=$item->image}
		{if $im}
		<a href="{$app->sys_base}tools/imga/{$im->id}" >
			<img class="gwPreview" data-image-url="{$app->sys_base}tools/imga/{$im->id}?size=200x200" src="{$app->sys_base}tools/imga/{$im->id}?size=40x19" align="absmiddle" vspace="2"  />
		</a>
		{/if}

	{/function}			
	
	
	
	{$do_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[invert_active,edit,delete,ext_actions]}
	

	
	{$dl_filters=$display_fields}
	
	{$order_enabled_fields = array_keys($display_fields)}
	
		{capture append=footer_hidden}
			<script>gwcms.initImagePreview();</script>
		{/capture}	
	
{/block}
