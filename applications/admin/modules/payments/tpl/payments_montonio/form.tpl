{include file="default_form_open.tpl"}



{$fields="order_id
amount
currency
test
remote_id
public_id
state
created_at
updated_at
completed_at
customer_id
email
phone
payment_id
payment_method
card_bin
card_country
card_last_four
card_expiry
cardholder_name
card_brand
checks
insert_time
update_time"}

{$fields = explode("\n", $fields)}
{foreach $fields as $field}
	{call e readonly=1}
{/foreach}


{include file="default_form_close.tpl"}