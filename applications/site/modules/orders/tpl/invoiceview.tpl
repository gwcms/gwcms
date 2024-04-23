{$invoiceargs=[]}
{if $preinvoice || $smarty.get.preinvoice}
	{$invoiceargs.preinvoice=1}
{/if}

{if $app->user && $app->user->isRoot()}
	
		<div class="btn-group">
			<a href="#" class="g-ml-5 text-uppercase dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='color:orange'>
				[ADM]
			</a>
			<div class="dropdown-menu pull-right dropdown-menu-right" style="">

				<a class="dropdown-item " href="{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id,html=>1,head=>1]+$invoiceargs,['carry_params'=>1])}">Debug html</a>
				<a class="dropdown-item " href="{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id,vars=>1]+$invoiceargs,['carry_params'=>1])}">Debug vars</a>
			</div>
		</div>	
	
{/if}

{if !$nodownload}
<div style="text-align:right;margin-bottom:5px">
	<a class='btn btn-warning btn-sm' href='{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id,download=>1]+$invoiceargs,['carry_params'=>1])}'> <i class="fa fa-download" aria-hidden="true"></i> {GW::ln('/m/DOWNLOAD_INVOICE')}</a>	
</div>
{/if}
<iframe src="{$app->buildUri('direct/orders/orders/invoice',['id'=>$item->id]+$invoiceargs,['carry_params'=>1])}" style="width:100%;height:600px"></iframe>
