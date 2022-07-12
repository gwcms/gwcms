{include "default_open.tpl"}



{if $item->extension=='svg'}
	<img src='/repository/{$item->relpath}'>
{elseif $item->type=='image'}
	<img class="file" data-file="{$file}" 
	     src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size=1000x600" 
	     title="{$item->filename}" alt="{$item->filename}" />
	
	{$url="{GW::s("SITE_URL")}/tools/img_resize?file={$item->relurl}&size=300x300"}
	
	
	<br><br>
	resize tool examples:<br>
	<a href="{$item->resize_url}&size=300x300" target="_blank">resized 300x300</a><br>
	<a href="{$item->resize_url}&size=300x300&method=crop" target="_blank">resize,crop</a><br>
	<a href="{$item->resize_url}&size=300x300&method=crop&filters=tint:ff000;brightness:50" target="_blank">resize,crop,tint,brighten</a><br>
		
{elseif $item->extension==pdf}
	<div style="height: 100vh;">
	<object class="fullsize" type="application/pdf" data="/repository/{$item->relpath}" style="width:100%;height:100%"></object>
	</div>
{elseif in_array($item->extension,['mp3'])}
	<audio id='audio' controls ><source src='/repository/{$item->relpath}' type='audio/mpeg'>Your browser does not support the audio element.</audio>
{elseif in_array($item->extension,['mp4'])}
	<video controls autostart  style="width:100%;height:100%">
		<source src="/repository/{$item->relpath}" type="video/mp4">
		Your browser does not support the video tag.
	      </video>
{else}
	Unsupported type, contact vidmantas.norkus@gw.lt to implement
	{d::ldump([
		extension=>$item->extension,
		item=>$item->toArray()
	])}
{/if}

<hr>
extension: {$item->extension}<br>
type: {$item->type}<br>
relpath: {$item->relpath}<br>


{include "default_close.tpl"}
