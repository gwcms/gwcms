{if isset($request->path_arr[1])}
{include_php file="modules/flashresponse.php"}

<?xml version="1.0"?>
	<product>
		<nr>{$nr}</nr>
		<w>{$imageListe[0]->image->width}</w>
		<h>{$imageListe[0]->image->height}</h>
		<sides>
			{foreach $imageListe as $item}
			<side>
			<nr>{if $item->type == 'front'}1{elseif $item->type == 'left'}2{elseif $item->type == 'right'}3{else}4{/if}</nr>
			<img>tools/img.php?id={$item->image->key}</img>
			{$item->config}
			{$item->design}
			</side>
			{/foreach}
		</sides>
	</product>
	{php}
		exit;
	{/php}
{else}
Restricted area.
{/if}
