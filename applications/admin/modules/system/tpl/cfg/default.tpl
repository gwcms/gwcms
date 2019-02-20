{include file="default_form_open.tpl"}

{*$nowrap=1*}


{call e field=project_url type=text title="Project url"}
{call e field=autostart_system_process type=bool title="Autostart system.php daemon"}

{call e field=max_tasks_history_length type=number}

{call e field=google_project_id type=number}
{call e field=google_api_access_key type=text}


{call e field=mail_from type=text default="Title <email@address.lt>"}
{call e field=mail_admin_emails type=text}
{call e field=mail_insert_succ type=bool}

{call e field=mail_is_smtp type=bool stateToggleRows="smtpdetails"}


{capture assign=tmp}
	<table>
{call e field=mail_smtp_host type=text}
{call e field=mail_smtp_user type=text}
{call e field=mail_smtp_pass type=text}
{call e field=mail_smtp_port type=number}
	</table>
{/capture}

{call e field=smtp_config type=read value=$tmp rowclass="smtpdetails"}


{include file="default_form_close.tpl" submit_buttons=[save]}