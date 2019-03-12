{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=autologin type=bool}



{if $app->user->isRoot()}
	{call e field=fields_enabled options=$options.fields_enabled type=multiselect note="(Root only)"}
	{*type=tags*}
	{call e field=available_fields  default='phone,surname,email,birth_date,gender,address,city,image,login_count,last_ip,last_request_time' note="(Root only)"}
{/if}



{include file="default_form_close.tpl" submit_buttons=[save]}