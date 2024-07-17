{include "default_open.tpl"}
{include "inputs/inputs.tpl"}

      <section class="container g-pt-50 g-pb-20">
        <div class="row justify-content-between">
          <div class="col-md-12 col-lg-10 order-lg-2 g-mb-80" style="float:none;margin:auto;">
	<div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-pt-50 g-pb-20 mb-4">
              <header class="text-center mb-4">
                <h1 class="h4 g-color-black g-font-weight-400">{GW::ln('/m/STATUS_CHANGE')}</h1>
              </header>

              <!-- Form -->
	      {*site url idejau nes ateina su // pradzioje, arba ten sutvarkyt shop/module_products.class.php doAfterBuyExecutorEmail*}
              <form id="addParcel" class="g-py-15" role="form" action="{$smarty.server.REQUEST_URI}" method="post"  >
		      <input type="hidden" name="act" value="doStatusChange" />
		
		      <center>
<table class="orderlist">
	<tr>
		<th>{GW::ln('/m/ORDER_ID')}</th>
		<th>{GW::ln('/m/ORDER_TIME')}</th>
		<th>{GW::ln('/m/ITEM_TITLE')}</th>
		<th>{GW::ln('/m/ITEM_STATUS')}</th>
	</tr>
		
{foreach $list as $item}
	<tr>
		<td>{$item->group_id}</td>
		<td>{if $item->tmporder->pay_time=='0000-00-00 00:00:00'}{$item->tmporder->placed_time}{else}{$item->tmporder->pay_time}{/if}</td>
		<td>
			{$item->qty} x {$item->invoice_line2}
		</td>
		<td style='padding-top:17px;'>
			{$status_opt=GW::ln('/m/status')}
			{$status_opt2[4]=$status_opt[4]}
			{$status_opt2[5]=$status_opt[5]}
			{$status_opt2[8]=$status_opt[8]}
			
			
			{call name=input field="status/{$item->id}" type=select options=$status_opt2 required=1 empty_option=1 value=$item->status title=false}
		</td>
		
	</tr>
{/foreach}

</table>
	</center>

		  
                </div>
		  
		

               
               <div class="mb-5">
                  <input class="btn btn-block u-btn-primary g-font-size-12 text-uppercase g-py-12 g-px-25" type="submit" value="{GW::ln('/m/SUBMIT_CHANGES')}">
                </div>
          </div>
        </div>
      </section>

		

<style>
	.orderlist{ border-collapse: collapse; }
	.orderlist td, .orderlist td,.orderlist th, .orderlist th{ padding: 4px 10px 4px 10px;  }
	.orderlist td{ border: 1px solid silver; }
	
</style>		



{include "default_close.tpl"}