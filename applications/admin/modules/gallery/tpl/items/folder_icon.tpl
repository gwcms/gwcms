<a href="{$link}" style="width:128px; height:128px; background-image:url('{$src}'); display: block;">

<div style="padding: 40px 21px">
{$subitems=$item->getChilds([limit=>4, type=>0])}
{foreach $subitems as $subitem}
	{$img=$subitem->image}
	<img src="{$app->sys_base}tools/imga/{$img->id}?size=40x25&method=crop" style="float:left;margin:1px 1px" />
{/foreach}
</div>

</a>