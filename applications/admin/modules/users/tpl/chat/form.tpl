{include file="default_form_open.tpl"}

{call e field=title}
{call e field=member_ids type=multiselect_ajax modpath="users/usr" preload=1 options=[]}

{if $is_root_chat_admin}
	{call e field=type type=select options=$options.room_type}
	{call e field=creator_id type=select_ajax modpath="users/usr" preload=1 options=[]}
	{call e field=is_active type=bool}
	{call e field=room_history_limit type=number}
{/if}

{include file="default_form_close.tpl" extra_fields=[id,creator_id,direct_key,last_message_id,last_message_time,last_event_id,last_event_time,insert_time,update_time]}
