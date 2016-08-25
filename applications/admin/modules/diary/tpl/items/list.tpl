{extends file="default_list.tpl"}
{include file="`$m->tpl_dir`/default_list_ajax_edit.tpl"}

{block name="init"}


	<style>
		.editable{
			max-width: 1400px;
			word-wrap: break-word;			
		}
	</style>

	

	{function name=dl_cell_text}


		{if $item->type==0}
			{$weekdays=['','Pirmadienis','Antradienis','Trečiadinis','Ketvirtadienis','Penktadienis','Šeštadienis','Sekmadienis']}
			<b>{$app->fh()->shortTime($item->time)} {$weekdays[date('N',strtotime($item->time))]}</b>
		{/if}

		<div class="editable" ajaxsaveargs="{ name: 'text', vals: {  id: {$item->id} } }">
		
		{if $item->type!=0}
			<img align="absmiddle" onclick="$(this).next().click()" src="{$app->icon_root}folder.png">
			<a href="{gw_link params=[pid=>$id] path_only=1}">{$item->text} ({$item->child_count})</a>
		{else}
			{GW_Link_Helper::parse($item->text)}
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