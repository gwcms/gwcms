{assign var=form_width value="800px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}






{$fields_config=[
	cols=>1,
	fields=>[
		pay_type=>[type=>text],
		user_id=>[type=>select_ajax,modpath=>"users/usr", preload=>1,options=>[],default=>$app->user->id],
		amount_total=>[type=>number,step=>0.01],
		amount_items=>[type=>text],
		adm_processed=>[type=>bool],
		extra=>[type=>read],
		active=>[type=>bool],
		company=>[type=>text],
		company_code=>[type=>text],
		company_addr=>[type=>text],
		vat_code=>[type=>text],
		name=>[type=>text],
		surname=>[type=>text],
		city=>[type=>text],
		email=>[type=>text],
		pay_test=>[type=>bool],
		active=>[type=>bool],
		secret=>[type=>read],
		adm_message=>[type=>text]
	]
]}

{if $m->feat(discountcode)}
	{$fields_config.fields.discount_id=[type=>select_ajax,modpath=>"products/discountcode",preload=>1,options=>[], after_input_f=>editadd]}
	{$fields_config.fields.amount_discount=[type=>text]}
	{$fields_config.fields.amount_coupon=[type=>text]}
{/if}


{if $item->pay_type=='banktransfer'}
	{$fields_config.fields.pay_user_msg=[type=>text]}
	{$fields_config.fields.banktransfer_confirm=[type=>image]}
{/if}

{if $item->pay_type}
	{$fields_config.fields.pay_confirm_id = [type=>select_ajax, modpath=>"payments/payments_{$item->pay_type}",preload=>1,options=>[], after_input_f=>editadd]}
{/if}




{$status_opts=[
	1=>GW::ln("/M/orders/status/1"),
	2=>GW::ln("/M/orders/status/2"),
	3=>GW::ln("/M/orders/status/3"),
	4=>GW::ln("/M/orders/status/4"),
	5=>GW::ln("/M/orders/status/5"),
	6=>GW::ln("/M/orders/status/6"),
	7=>GW::ln("/M/orders/status/7"),
	8=>GW::ln("/M/orders/status/8")
]}


{call e field=status type=select options=$status_opts empty_option=1}

{if $smarty.get.shift_key}
	{$fields_config.fields.payment_status=[type=>number]}
	{$fields_config.fields.pay_time=[type=>text]}
	{$fields_config.fields.mail_accept=[type=>bool]}
{else}
	{$fields_config.fields.pay_time=[type=>read]}
	{$fields_config.fields.mail_accept=[type=>read]}
	{$fields_config.fields.payment_status=[type=>read]}
{/if}

{capture assign=tmp}

		<table class="gwTable">
			<tr>
				<th>Type</th>
				<th>Title</th>
				<th>Qty</th>
				<th>Unit price</th>
				<th>Total</th>
			</tr>
					
	{foreach $item->items as $oitem}
		<tr>
			<td>{$oitem->type}</td>
			<td>{$oitem->invoice_line}</td>
			<td>{$oitem->qty}</td>
			<td>{$oitem->unit_price}</td>
			<td>{$oitem->total}</td>
		</tr>
	{/foreach}
		</table>
{/capture}
	{$fields_config.fields.items=[type=>read, value=>$tmp,layout=>wide,title=>false]}



{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}







{assign var="comments" value=1 scope=global}


<style>
	.input_label_td{ width: 120px !important; }
	.input_td{ width: 300px; }
</style>

