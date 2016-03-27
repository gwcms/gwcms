{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=project_url type=text title="Project url"}
{include file="elements/input.tpl" name=autostart_system_process type=bool title="Autostart system.php daemon"}

{include file="elements/input.tpl" name=max_tasks_history_length type=number}

{include file="elements/input.tpl" name=google_project_id type=number}
{include file="elements/input.tpl" name=google_api_access_key type=text}





{include file="default_form_close.tpl" submit_buttons=[save]}