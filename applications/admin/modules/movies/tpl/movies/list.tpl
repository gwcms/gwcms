{extends file="default_list.tpl"}


{block name="init"}



	{function name=dl_cell_image}		
			{$img=$item->image1}
			
			{if $img->id}
				<a href="{$app->sys_base}tools/imga/{$img->id}">
					<img src="{$app->sys_base}tools/imga/{$img->id}?size=50x50" align="absmiddle" vspace="2"  />
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
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,updateimdball]}			
	{$dl_actions=[edit,delete,ext_actions]}

{function name=do_toolbar_buttons_updateimdball}
	
	{toolbar_button title="Update all not updated imdb" iconclass='gwico-Download' href=$m->buildUri(false,[act=>doUpdateAllWithoutImdb])}
{/function}
	
{/block}