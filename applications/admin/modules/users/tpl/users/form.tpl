{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=username}
{include file="elements/input.tpl" name=email}
{include file="elements/input.tpl" name=name}
{include file="elements/input.tpl" name=surname}
{include file="elements/input.tpl" name=phone}
{include file="elements/input.tpl" name=city}
{include file="elements/input.tpl" name=address}



{include file="elements/input.tpl" type=bool name=active}
{include file="elements/input.tpl" type=multiselect name=group_ids options=$groups_options}
{include file="elements/input.tpl" type=select name=session_validity options=$m->lang.SESSION_VALIDITY_OPT}


{include file="elements/input.tpl" type=pass_visible name=pass_new title=$m->lang.FIELDS.pass}




{include file="default_form_close.tpl" extra_fields=[id,login_time,login_count,last_ip,insert_time,update_time]}
