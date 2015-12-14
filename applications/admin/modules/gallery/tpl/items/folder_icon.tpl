<a href="{$link}" style="width:128px; height:128px; background-image:url('{$src}'); display: block;">

<div style="padding: 40px 21px">
	
{if $item->image}
	{$subitems=$item->getChilds([limit=>10, type=>0])}
	{$x=array_unshift($subitems, $item)}
{/if}
{$cnt=0}

{foreach $subitems as $subitem}
	{if $cnt<4}
		{$img=$subitem->image}
		{if $img}
			<img src="{$app->sys_base}tools/imga/{$img->id}?size=40x25&method=crop" style="float:left;margin:1px 1px" />
			{$cnt=$cnt+1}
		{/if}
	{/if}
{/foreach}
</div>

</a>