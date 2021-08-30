{*assign var=form_width value="100%" scope=global*}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}



{$fields_config=[
	cols=>1,
	fields=>[
		pay_type=>[type=>text],
		user_id=>[type=>select_ajax,modpath=>"users/usr", preload=>1,options=>[],default=>$app->user->id],
		pay_confirm_id=>[type=>select_ajax, modpath=>"payments/payments_paysera",preload=>1,options=>[], after_input_f=>editadd],
		amount_total=>[type=>number,step=>0.01],
		adm_processed=>[type=>bool],
		extra=>[type=>text]
	]
]}


{if $item->pay_type=='banktransfer'}
	{$fields_config.fields.pay_user_msg=[type=>text]}
	{$fields_config.fields.banktransfer_confirm=[type=>image]}
{/if}

{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}








<style>
	.input_label_td{ width: 120px !important; }
	.input_td{ width: 300px; }
</style>