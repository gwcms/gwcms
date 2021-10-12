{include file="default_open.tpl"}


{if $smarty.get.invoiceview}
	{include file="`$smarty.current_dir`/invoiceview.tpl"}
{else}
	{include "`$smarty.current_dir`/prepareinvoice_0.tpl"}
{/if}


{include file="default_close.tpl"}


