{include file="default_form_open.tpl" form_width="800px"}


{call e field=user_id type=select_ajax modpath="customers/users"  preload=1 options=[]}
{call e field=validfrom type=datetime}
{call e field=expires type=datetime}


{if $item->insert_time >= "2021-10-07"}
	{$tmp = "payments/ordergroups"}
{else}
	{$tmp = "payments/payments_paysera"}
{/if}	
			
{call e field=pay_id type=select_ajax modpath=$tmp  preload=1 options=[] after_input_f=editadd}



{call e field=test type=bool}
{call e field=notes type=text}

{call e field=active type=bool}






{include file="default_form_close.tpl"}