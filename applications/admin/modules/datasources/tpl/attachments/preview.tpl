{include "default_open.tpl"}

{$file=$item->file}
{$filename=pathinfo($file->original_filename)}

{if $item->content_cat=='image'}
	{$thumbn_sz=800x600}
	{$image=$item->image}
	<img src="{$app->sys_base}tools/imga/{$image->id}?size={$thumbn_sz}" align="absmiddle" vspace="2" />
{elseif $item->attachment->extension=='svg'}
	<img src='{$app->sys_base}tools/download/{$item->attachment->key}?view=1' style="max-width: 600px;max-height:600px;">
{elseif strpos($item->content_type,'pdf')!==false}
	
	
	
	{$title=$filename.filename|truncate:40}
	{if $filename.extension}
		{$title="`$title`.`$filename.extension`"}
	{/if}

	{*{$title|escape} ({$file->size_human}) {$item->content_type}*}

	{*<a href='{$app->sys_base}tools/download/{$file->key}'>down</a>*}
	
	<object class="fullsize" type="application/pdf" data="{$app->sys_base}tools/download/{$file->key}?view=1"></object>
{elseif in_array($item->attachment->getType(),['mp3'])}
	{$file=$item->attachment}
	<audio id='audio' controls ><source src='{$app->sys_base}tools/download/{$file->key}?view=1' type='audio/mpeg'>Your browser does not support the audio element.</audio>
{elseif in_array($filename.extension,[txt,json,dat])}
	<textarea style='width:100%;height:80vh'>{$item->file->getContents()|escape}</textarea>
{elseif in_array($filename.extension,[csv])}
	<pre style='width:100%;height:80vh'>{$item->file->getContents()|escape}</pre>
{else}
	Unsupported type, contact vidmantas.norkus@gw.lt to implement
	{d::ldump($item->toArray())}
	{d::ldump($item->attachment->toArray())}
	{d::ldump($filename)}

{/if}

<style>
	.fullsize{ 
		height: calc(100vh - 5px); 
		width: calc( 100vw - 5px);
	}
</style>

{include "default_close.tpl"}
