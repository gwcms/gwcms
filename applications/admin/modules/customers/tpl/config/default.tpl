{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=customer_group type=select options=$options.customer_group empty_option=1}


{call e field=login_with_fb type=bool}
{call e field=fb_app_id type=text}
{call e field=fb_app_secret type=password}



{call e field=fb_use_auth_gw type=bool}
{call e field=gg_use_auth_gw type=bool}




{call e field="verify_mail_tpl_id" type=select_ajax modpath="emails/email_templates" preload=1  options=[]  source_args=[byid=>1] after_input_f="editadd"}
{call e field="pass_change_mail_tpl_id" type=select_ajax modpath="emails/email_templates" preload=1  options=[]  source_args=[byid=>1] after_input_f="editadd"}



{if $app->user->isRoot()}
	{call e field=registration_fields_required options=$options.fields_enabled type=multiselect note="(Root only)" 	value_format=json1}
	{call e field=registration_fields_optional options=$options.fields_enabled type=multiselect note="(Root only)" 	value_format=json1}	
	{*type=tags*}
	{call e field=available_fields  default='phone,name,surname,email,birth_date,gender,city,image,company_name,country,agreetc' note="(Root only)"}
	


	
	{call e field="superadmin_group" type=select_ajax modpath="users/groups" preload=1 options=[]}
	{call e field="features" type=multiselect options=GW::l('/m/OPTIONS/features') value_format=json1}
{/if}



{include file="default_form_close.tpl" submit_buttons=[save]}