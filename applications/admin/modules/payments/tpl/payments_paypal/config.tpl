
{call e field=pay_test type=bool}
{call e field=paypal_email}
{call e field=default_currency_code default=EUR}
{call e field=paypal_test_email}

{call e field=test_user_group type=select_ajax modpath="users/groups" preload=1 options=[] empty_option=1}

{call e field=test_notes type=textarea}
