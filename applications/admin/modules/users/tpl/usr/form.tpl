{include file="default_form_open.tpl"}

{$efields=$m->getEnabledFields()}

{if $efields.image}
	{call e field=image type=image}
{/if}


{call e field=username}

{if $efields.email}
	{call e field=email}
{/if}

{if $efields.phone}
	{call e field=phone}
{/if}


{call e field=name}

{if $efields.surname}
	{call e field=surname}
{/if}

{if $efields.birth_date}
	{call e field=birth_date type=date}
{/if}

{if $efields.gender}
	{call e field=gender type=select options=GW::l('/m/OPTIONS/gender') empty_option=1}
{/if}


{if $efields.address}
	{call e field=address}
{/if}

{if $efields.city}
	{call e field=city}
{/if}

{*'phone','surname','email','birth_date','gender','address','city','image'*}


{call e field=group_ids type=multiselect options=$options.group_ids}
{call e field=session_validity type=select options=GW::l('/m/SESSION_VALIDITY_OPT')}




{call e field=description type=textarea height="100px"}
{call e field=active type=bool}
{call e field=pass_new type=pass_visible title=GW::l('/m/FIELDS/pass')}

{if $m->rootadmin}
	{call e field=is_admin type=bool}
	{call e field=parent_user_id type=select options=$options.parent_user_id empty_option=1 default=$app->user->id}
{/if}


{include file="default_form_close.tpl" extra_fields=[id,login_time,login_count,last_ip,insert_time,update_time,referer]}
