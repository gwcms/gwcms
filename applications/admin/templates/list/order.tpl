{if !$title}
	{$title=$app->fh()->fieldTitle($name)}
{/if}

{$order=$m->calcOrder($name)}

{if $smarty.get.print_view}
	{$title}
{else}
	{if $order.current}<img style="padding: 2px" src="{$app->icon_root}order_{$order.current}.png" align="absmiddle" onclick="$(this).next().click()" />{/if}
	<a href="{$order.uri}" {if $order.current}style="font-weight:bold"{/if}>{$title}{if $order.multiorder} ({$order.multiorder}){/if}</a>
{/if}