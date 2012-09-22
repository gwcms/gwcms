{capture name=info}{$lang.DIMENSIONS}: {$image->width}x{$image->height}, {$lang.FILE_SIZE}: {GW_Math_Helper::cfilesize($image->size)}{/capture}

<a href="tools/img.php?id={$image->id}" target="_blank">
	<img title="{$smarty.capture.info|escape}" src="tools/img.php?id={$image->id}&width={$width}&height={$height}" border="{$border|default:0}" />
</a>
