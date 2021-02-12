{include "default_open.tpl"}

{if $item->type=='image'}
	<img class="file" data-file="{$file}" 
	     src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size=1000x600" 
	     title="{$item->filename}" alt="{$item->filename}" />
{elseif $item->extension=='svg'}
	<img src='/repository/{$item->relpath}'>
{elseif $item->extension==pdf}
	<div style="height: 100vh;">
	<object class="fullsize" type="application/pdf" data="/repository/{$item->relpath}" style="width:100%;height:100%"></object>
	</div>
{elseif in_array($item->extension,['mp3'])}
	<audio id='audio' controls ><source src='/repository/{$item->relpath}' type='audio/mpeg'>Your browser does not support the audio element.</audio>
{else}
	Unsupported type, contact vidmantas.norkus@gw.lt to implement
	{d::ldump([
		extension=>$item->extension,
		item=>$item->toArray()
	])}
{/if}


{include "default_close.tpl"}
