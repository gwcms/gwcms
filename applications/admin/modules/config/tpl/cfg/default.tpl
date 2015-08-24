{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=project_url type=text title="Project url"}
{include file="elements/input.tpl" name=autostart_system_process type=bool title="Autostart system.php daemon"}




{include file="default_form_close.tpl" submit_buttons=[save]}