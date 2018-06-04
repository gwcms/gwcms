{capture name=info}{$lang.DIMENSIONS}: {$image->width}x{$image->height}, {$lang.FILE_SIZE}: {GW_Math_Helper::cfilesize($image->size)} {if $show_filename}{$image->original_filename}{/if}{/capture}

<a href="{$app->sys_base}tools/img/{$image->key}" target="_blank">
	<img title="{$smarty.capture.info|escape}" src="{$app->sys_base}tools/img/{$image->key}?size={$width}x{$height}" border="{$border|default:0}" />
</a>
