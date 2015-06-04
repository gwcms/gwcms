{extends file="default_list.tpl"}


{block name="init"}



	{function name=dl_cell_image}
		{$image=$item->image}
		{if $image}

			{capture assign="poptext"}<img src="repository/{$item->image}" />{/capture}
			<img src="repository/{$item->image}" class="tooltip" title="{$poptext|escape}" height="50px" align="absmiddle" vspace="2" title="{$item->title|escape}" />
		{else}
			{$img=$item->image1}
			{if $img->id}
			{capture assign="poptext"}<img src="tools/img.php?id={$img->id}" />{/capture}
			<img src="tools/img.php?id={$img->id}&size=50x50" class="tooltip" title="{$poptext|escape}" />
			{/if}
		{/if}
	{/function}
	
	{function name=dl_cell_title}
		<a href="{$ln}/{GW::$request->page->path}/{$item->id}/form" class="tooltip" title="{$item->description}">{$item->title|default:"No title"}</a>		
	{/function}
	
	{function dl_cell_insert_time}
		{FH::shortTime($item->insert_time)}
	{/function}
		

	{$dl_smart_fields=[image,title,insert_time]}
	
	
	
	{$dl_fields=$m->getDisplayFields([image=>1, title=>1,rate=>1,insert_time=>1,update_time=>1])}
	
	
	
	{$dl_toolbar_buttons[] = dialogconf}	
	
	{$dl_actions=[delete]}
	
	{$dl_filters=[image=>1, title=>1, insert_time=>1, active=>[type=>select, options=>$lang.ACTIVE_OPT]]}
	


	
	{$dl_order_enabled_fields=[title,insert_time,update_time,rate]}
{/block}