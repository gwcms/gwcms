{extends file="default_list.tpl"}


{block name="init"}

	
{$display_fields=[
	orderid=>1,
	handler=>1,
	action=>1,
	payment_status=>1,
	payment_type=>1,
	ipn_track_id=>1,
	item_name=>1,
	custom=>1,
	mc_gross=>1,
	mc_fee=>1,
	tax=>1,
	payment_fee=>1,
	handling_amount=>1,
	shipping=>1,
	payment_gross=>1,
	quantity=>1,
	item_number=>1,
	mc_currency=>1,
	txn_id=>1,
	txn_type=>1,
	transaction_subject=>1,
	receiver_id=>1,
	business=>1,
	receiver_email=>1,
	payer_email=>1,
	payer_id=>1,
	payer_status=>1,
	first_name=>1,
	last_name=>1,
	address_name=>1,
	address_street=>1,
	address_zip=>1,
	address_country_code=>1,
	residence_country=>1,
	address_country=>1,
	address_state=>1,
	address_city=>1,
	address_status=>1,
	payment_date=>1,
	protection_eligibility=>1,
	test_ipn=>1,
	extra=>1,
	handler_state=>1,
	insert_time=>1,
	update_time=>1,
	handler_state=>1,
	insert_time=>1,
	update_time=>1
]}



	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	{$dl_toolbar_buttons[]=dialogconf}	
	
	{$dl_actions=[edit,delete]}
	
	{$dl_filters=$display_fields}
	
	
	{function name=dl_cell_extra}
		{json_encode($item->extra)}
	{/function}
	
	{$dl_smart_fields=[extra]}
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}