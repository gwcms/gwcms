{$invoiceargs=[]}
{if $preinvoice || $smarty.get.preinvoice}
	{$invoiceargs.preinvoice=1}
{/if}

{if !$nodownload}
<div style="text-align:right;margin-bottom:5px">
	<a class='btn btn-warning btn-sm' href='{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id,download=>1]+$invoiceargs)}'> <i class="fa fa-download" aria-hidden="true"></i> {GW::ln('/m/DOWNLOAD_INVOICE')}</a>	
</div>
{/if}
<iframe src="{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id]+$invoiceargs)}" style="width:100%;height:600px"></iframe>
