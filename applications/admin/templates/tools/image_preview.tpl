{capture name=info}{$lang.DIMENSIONS}: {$image->width}x{$image->height}, {$lang.FILE_SIZE}: {GW_Math_Helper::cfilesize($image->size)}{/capture}

<a href="{$app->sys_base}tools/imga/{$image->id}" target="_blank">
	<img title="{$smarty.capture.info|escape}" src="{$app->sys_base}tools/imga/{$image->id}?size={$width}x{$height}" border="{$border|default:0}" />
</a>
