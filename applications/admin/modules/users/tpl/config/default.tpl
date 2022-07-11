{include file="default_form_open.tpl" form_width="900px"}

{*$nowrap=1*}


{call e field=autologin type=bool}



{if $app->user->isRoot()}
	{call e field=fields_enabled options=$options.fields_enabled type=multiselect note="(Root only)"}
	{*type=tags*}
	{call e field=available_fields  default='phone,surname,email,birth_date,gender,address,city,image,login_count,last_ip,last_request_time' note="(Root only)"}
	
	
	{call e field=userapi_userpass type=text note="format: 'user:pass' (Root only)"}
	{call e field=secondcms_userservice_endpoint type=text note="format: 'https://user:pass@domain.tld/service/user' (Root only)" hidden="used to import user if not exist in primary cms"}

	{call e field=login_with_fb type=bool}
	{call e field=login_with_gg type=bool}
	
	
	{call e field="superadmin_group" type=select_ajax modpath="users/groups" preload=1 options=[]}
	
{/if}


{include file="default_form_close.tpl" submit_buttons=[save]}