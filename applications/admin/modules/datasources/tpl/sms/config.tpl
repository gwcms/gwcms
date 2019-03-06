{include file="default_form_open.tpl" changes_track=1 form_width="500px" action="saveConfig"}



{call e field=username}
{call e field=user_id}
{call e field=api_key type=password}


{include file="default_form_close.tpl" submit_buttons=[save]}