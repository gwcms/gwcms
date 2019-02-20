{include file="default_form_open.tpl"}


{call e field=image type=image}


{call e field=username}
{call e field=email}
{call e field=phone}


{call e field=name}
{call e field=surname}

{*
{call e field=birth_date type=date}
*}
{call e field=gender type=select options=GW::l('/m/OPTIONS/gender') empty_option=1}



{call e field=group_ids type=multiselect options=$options.group_ids}
{call e field=session_validity type=select options=$m->lang.SESSION_VALIDITY_OPT}




{call e field=description type=textarea height="100px"}
{call e field=active type=bool}
{call e field=pass_new type=pass_visible title=$m->lang.FIELDS.pass}

{if $m->rootadmin}
	{call e field=is_admin type=bool}
	{call e field=parent_user_id type=select options=$options.parent_user_id empty_option=1 default=$app->user->id}
{/if}


{include file="default_form_close.tpl" extra_fields=[id,login_time,login_count,last_ip,insert_time,update_time,referer]}
