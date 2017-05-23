{if !$title}
	{$title=$app->fh()->fieldTitle($name)}
{/if}

{$order=$m->calcOrder($name)}

{if $smarty.get.print_view}
	{$title}
{else}
	{if $order.current}<i class="fa fa-sort-amount-{$order.current}" onclick="$(this).next().click()" ></i>{/if}
	<a href="{$order.uri}" {if $order.current}style="font-weight:bold"{/if}>{$title}{if $order.multiorder} ({$order.multiorder}){/if}</a>
{/if}