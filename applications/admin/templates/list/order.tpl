{if !$title}
	{$title=$app->fh()->fieldTitle($name)}
{/if}

{$order=$m->calcOrder($name)}

{if $smarty.get.print_view}
	{$title}
{else}
	{if $order.current}<i class="fa fa-sort-amount-{$order.current}" onclick="$(this).next().click()" ></i>{/if}
	<a class="setOrder" data-order="{$order.order}" href="#" {if $order.current}style="font-weight:bold"{/if}>
		{$title}{if $order.multiorder} ({$order.multiorder}){/if}</a>
{/if}




{if !GW::$globals.smarty_orderinitdone}
	
	{capture append=footer_hidden}
		<script>
			require(['gwcms'], function(){


				var baseurl = "{$app->buildUri(false,[act=>doSetOrder],[carry_params=>1])}";
				$('.setOrder').click(function(e){ 

					var args = { "order": $(this).data('order') };

					//bus pridedamas kaip papildomas
					if(e.shiftKey==true){
						args['shift']=1;
					}

					e.preventDefault();

					var url = gw_navigator.url(baseurl, args);
					location.href = url;
					//alert(url);

					return false;
				})
			})
		</script>	
	{/capture}
	
	{$GLOBALS.smarty_orderinitdone=1}
{/if}