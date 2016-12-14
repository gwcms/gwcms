{extends file="default_list.tpl"}


{block name="init"}



	{function name=dl_cell_image}
			{$img=$item->image1}
			{d::ldump($img)}
			{if $img->id}
				<a href="{$app->sys_base}tools/imga/{$img->id}v={$item->v}">
					<img src="{$app->sys_base}tools/imga/{$img->id}?size=32x32" align="absmiddle" vspace="2"  />
				</a>
			{/if}
	{/function}
	
	{function name=dl_cell_title}
		<a href="{$ln}/{$app->page->path}/{$item->id}/form" title="{$item->description}">{$item->title|default:"No title"}</a>		
	{/function}
	
	{function dl_cell_insert_time}
		{$app->fh()->shortTime($item->insert_time)}
	{/function}
		

	{$dl_smart_fields=[image,title,insert_time]}
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}			
	
	{$dl_actions=[edit,delete,ext_actions]}
	
	{$dl_filters=[image=>1, title=>1, insert_time=>1, active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
	


	
	{$dl_order_enabled_fields=[title,insert_time,update_time,rate]}
{/block}