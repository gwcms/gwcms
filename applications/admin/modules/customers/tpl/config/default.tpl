{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=customer_group type=select options=$options.customer_group empty_option=1}

{include file="elements/input.tpl" name=login_with_fb type=bool}
{include file="elements/input.tpl" name=fb_app_id type=text}
{include file="elements/input.tpl" name=fb_app_secret type=password}




{include file="default_form_close.tpl" submit_buttons=[save]}