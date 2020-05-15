{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=customer_group type=select options=$options.customer_group empty_option=1}

{call e field=login_with_fb type=bool}
{call e field=fb_app_id type=text}
{call e field=fb_app_secret type=password}

{call e field=use_auth_gw_lt type=bool}


{include file="default_form_close.tpl" submit_buttons=[save]}