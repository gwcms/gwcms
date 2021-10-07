{*assign var=form_width value="100%" scope=global*}

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
		extra=>[type=>text],
		active=>[type=>bool],
		company=>[type=>text],
		company_code=>[type=>text],
		company_addr=>[type=>text],
		vat_code=>[type=>text],
		name=>[type=>text],
		surname=>[type=>text],
		city=>[type=>text],
		email=>[type=>text],
		pay_test=>[type=>bool]
	]
]}



{if $item->pay_type=='banktransfer'}
	{$fields_config.fields.pay_user_msg=[type=>text]}
	{$fields_config.fields.banktransfer_confirm=[type=>image]}
{/if}

{if $item->pay_type}
	{$fields_config.fields.pay_confirm_id = [type=>select_ajax, modpath=>"payments/payments_{$item->pay_type}",preload=>1,options=>[], after_input_f=>editadd]}
{/if}


{if $smarty.get.shift_key}
	{$fields_config.fields.payment_status=[type=>number]}
	{$fields_config.fields.pay_time=[type=>text]}
	{$fields_config.fields.mail_accept=[type=>bool]}
{/if}

{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}








<style>
	.input_label_td{ width: 120px !important; }
	.input_td{ width: 300px; }
</style>