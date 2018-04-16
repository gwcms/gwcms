{extends file="default_list.tpl"}


{block name="init"}

	{$dl_inline_edit=1}
	

	{function name=dl_cell_image}	
		
		{$imdb = json_decode($item->imdb)}

		{if $imdb->poster_external}
			{capture assign=tmp}<div style="width:300px"><img src="{$imdb->poster_external_big}" style="height:300px;" align="absmiddle" vspace="2"  /></div>{/capture}
			
			<a href="{$imdb->poster_external_big}" class="add-tooltip" data-html="true" data-toggle="" href="#" data-original-title="{htmlspecialchars("`$tmp`")}" >
				<img src="{$imdb->poster_external}" style="max-height: 50px;max-height: 50px;" align="absmiddle" vspace="2"  />
			</a>
		{/if}		
		
		{$img=$item->image1}

		{if $img->id}


			<a target="_blank" href="{$imdb->poster}" {*href="{$app->sys_base}tools/imga/{$img->id}"*}>
				<img src="{$app->sys_base}tools/imga/{$img->id}?size=50x50" align="absmiddle" vspace="2"  />
			</a>
		{/if}
	{/function}
	
	{function name=dl_cell_description}
		<span title="{$item->description|escape}">{$item->description|truncate:40}</a>		
	{/function}
	
	{function dl_cell_insert_time}
		{$app->fh()->shortTime($item->insert_time)}
	{/function}
		

	{$dl_smart_fields=[image,description,insert_time]}
		
	{$do_toolbar_buttons = [addinlist]}
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,updateimdball]}			
	{$dl_actions=[edit,delete,ext_actions]}

{function name=do_toolbar_buttons_updateimdball}
	
	{toolbar_button title="Update all not updated imdb" iconclass='gwico-Download' href=$m->buildUri(false,[act=>doUpdateAllWithoutImdb])}
{/function}
	
{/block}