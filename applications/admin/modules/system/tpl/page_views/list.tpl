{extends file="default_list.tpl"}


{block name="init"}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden[] = dialogconf}
	{$dl_smart_fields=[insert_time,dropdown,calculate,default,fields,title_short,condition,order]}
	
	
	{if $app->user->isRoot()}
		
		{$dl_actions=[invert_active,editshift,delete,clone]}
	{else}
		{$dl_actions=[invert_active,edit,delete]}
	{/if}
	
	
	
	{$dl_filters=[]}
	{$dl_order_enabled_fields=[]}
	{$dl_inline_edit=1}
	
	
	{if $app->user->isRoot()}
		{function name=do_toolbar_buttons_migrate}
			{toolbar_button title="Migrate from old" iconclass="gwico-Rotate-Right-Filled" href=$m->buildUri(false,[act=>doMigrate])}	
		{/function}

		{$do_toolbar_buttons_hidden[] = migrate}	
	{/if}
	
	{function dl_cell_dropdown}
		<a href="{$m->buildUri(false,[act=>doInvertField,id=>$item->id,field=>dropdown])}">
			<i class="fa {if $item->dropdown}fa-check-square-o{else}fa-square-o{/if}"></i>
		</a>
	{/function}
	{function dl_cell_calculate}
		<a href="{$m->buildUri(false,[act=>doInvertField,id=>$item->id,field=>calculate])}">
			<i class="fa {if $item->calculate}fa-check-square-o{else}fa-square-o{/if}"></i>
		</a>
	{/function}

	{function dl_cell_default}
		<a href="{$m->buildUri(false,[act=>doInvertField,id=>$item->id,field=>default])}">
			<i class="fa {if $item->default}fa-dot-circle-o{else}fa-circle-o{/if}"></i>
		</a>
	{/function}
	
	{function dl_cell_fields}
		{$tmp=json_decode($item->fields, true)}
		{if $tmp}({count($tmp)}){/if}
	{/function}	

	{function dl_cell_title_short}
		{$item->title_short} {* allow html *}
	{/function}
	
	
	{function dl_cell_condition}
		{$item->condition|truncate:40}
	{/function}	
	
	{function dl_cell_order}
		{$item->order|truncate:40}
	{/function}	
	
	{function dl_cell_insert_time}
                {$x=explode(' ',$item->insert_time)}
		<span title=""{$x.1}>{$x.0}</span>
	{/function}	
{/block}
