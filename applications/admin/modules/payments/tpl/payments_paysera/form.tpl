{include file="default_form_open.tpl"}


{$fields="orderid
handler
action
paytext
p_firstname
p_lastname
p_email
amount
currency
country
test
payment
m_pay_restored
status
requestid
payamount
paycurrency
type
handler_state"}

{$fields = explode("\n", $fields)}
{foreach $fields as $field}
	{call e readonly=1}
{/foreach}


{include file="default_form_close.tpl"}