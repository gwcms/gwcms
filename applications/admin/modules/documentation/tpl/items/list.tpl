{extends file="default_list.tpl"}

{block name="init"}



	{function name=dl_cell_text}



		<div>
		
		{if $item->type!=0}
			<img align="absmiddle" onclick="$(this).next().click()" src="{$app->icon_root}folder.png">
			<a href="{gw_link params=[pid=>$id] path_only=1}">{$item->title} ({$item->child_count})</a>
		{else}
			{$item->title}
		{/if}
		</div>
		
	{/function}
	
	

	{$dl_smart_fields=[text]}
	{$dl_output_filters=[time=>short_time]}
	
	
	{$dl_fields=$m->getDisplayFields([text=>1,time=>1,insert_time=>0,update_time=>0])}
	{$do_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=[text=>1, time=>1]}
	


	
	{*$order_enabled_fields=[text,insert_time,update_time]*}
{/block}


{block "after_list"}

{/block}