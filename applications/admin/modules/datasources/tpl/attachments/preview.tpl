{include "default_open.tpl"}


{if $item->content_cat=='image'}
	{$thumbn_sz=800x600}
	{$image=$item->image}
	<img src="{$app->sys_base}tools/imga/{$image->id}?size={$thumbn_sz}" align="absmiddle" vspace="2" />
{elseif $item->content_type=='pdf'}
	{$file=$item->file}
	
	{$filename=pathinfo($file->original_filename)}
	{$title=$filename.filename|truncate:40}
	{if $filename.extension}
		{$title="`$title`.`$filename.extension`"}
	{/if}

	{*{$title|escape} ({$file->size_human}) {$item->content_type}*}

	{*<a href='{$app->sys_base}tools/download/{$file->key}'>down</a>*}
	
	<object class="fullsize" type="application/pdf" data="{$app->sys_base}tools/download/{$file->key}?view=1"></object>
{else}
	Unsupported type, contact vidmantas.norkus@gw.lt to implement
{/if}

<style>
	.fullsize{ 
		height: calc(100vh - 5px); 
		width: calc( 100vw - 5px);
	}
</style>

{include "default_close.tpl"}
