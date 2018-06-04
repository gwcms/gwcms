{include file="default_form_open.tpl"}

{*$nowrap=1*}


{include file="elements/input.tpl" name=project_url type=text title="Project url"}
{include file="elements/input.tpl" name=autostart_system_process type=bool title="Autostart system.php daemon"}

{include file="elements/input.tpl" name=max_tasks_history_length type=number}

{include file="elements/input.tpl" name=google_project_id type=number}
{include file="elements/input.tpl" name=google_api_access_key type=text}


{include file="elements/input.tpl" name=mail_from type=text default="Title <email@address.lt>"}
{include file="elements/input.tpl" name=mail_admin_emails type=text}
{include file="elements/input.tpl" name=mail_insert_succ type=bool}

{include file="elements/input.tpl" name=mail_is_smtp type=bool stateToggleRows="smtpdetails"}


{capture assign=tmp}
	<table>
{include file="elements/input.tpl" name=mail_smtp_host type=text}
{include file="elements/input.tpl" name=mail_smtp_user type=text}
{include file="elements/input.tpl" name=mail_smtp_pass type=text}
{include file="elements/input.tpl" name=mail_smtp_port type=number}
	</table>
{/capture}

{include file="elements/input.tpl" name=smtp_config type=read value=$tmp rowclass="smtpdetails"}


{include file="default_form_close.tpl" submit_buttons=[save]}