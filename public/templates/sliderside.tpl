{include "default_open.tpl"}
	{if isset($request->path_arr[3]) && $request->path_arr[3]['name'] == 'edit'}
		{include file="kundeflash.tpl"}
	{else}
		{include file="slider.tpl"}
	{/if}
{include "default_close.tpl"}