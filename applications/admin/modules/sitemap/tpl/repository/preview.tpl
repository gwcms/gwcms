
{if $item->type=='image'}
	
	

<img class="file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size=300x300" title="{$item->filename}" alt="{$item->filename}" />

{/if}