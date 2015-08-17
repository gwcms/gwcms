{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=username}
{include file="elements/input.tpl" name=email}
{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=surname}
{include file="elements/input.tpl" name=phone}
{include file="elements/input.tpl" name=city}
{include file="elements/input.tpl" name=address}



{include file="elements/input.tpl" type=bool name=active}
{include file="elements/input.tpl" type=multiselect name=group_ids options=$options.group_ids}
{include file="elements/input.tpl" type=select name=session_validity options=$m->lang.SESSION_VALIDITY_OPT}


{include file="elements/input.tpl" type=pass_visible name=pass_new title=$m->lang.FIELDS.pass}

{if $m->rootadmin}
	{include file="elements/input.tpl" name=parent_user_id type=select options=$options.parent_user_id empty_option=1 default=$app->user->id}
{/if}


{include file="default_form_close.tpl" extra_fields=[id,login_time,login_count,last_ip,insert_time,update_time]}
