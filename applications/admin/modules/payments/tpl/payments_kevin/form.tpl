{include file="default_form_open.tpl"}





{$fields="order_id
kevin_id
bankStatus
statusGroup
amount
currencyCode
description
amount
pm_creditorName
pm_endToEndId
pm_creditorAccount_iban
pm_creditorAccount_currencyCode
pm_debtorAccount_iban
pm_debtorAccount_currencyCode
pm_bankId
pm_paymentProduct
pm_requestedExecutionDate
test"}

{$fields = explode("\n", $fields)}
{foreach $fields as $field}
	{call e readonly=1}
{/foreach}


{include file="default_form_close.tpl"}