{include file="default_form_open.tpl" form_width="824px"}

{*$nowrap=1*}


{call e field=project_url type=text title="Project url"}

{call e field=autostart_system_process_env1 type=bool title="Autostart system.php daemon dev"}
{call e field=autostart_system_process_env3 type=bool title="Autostart system.php daemon prod"}

{call e field=max_tasks_history_length type=number}

{call e field=google_project_id type=number}
{call e field=google_api_access_key type=text}


{*
moved to emails module
{call e field=mail_from type=text default="Title <email@address.lt>"}
{call e field=mail_admin_emails type=text}
{call e field=mail_insert_succ type=bool}

{call e field=mail_is_smtp type=bool stateToggleRows="smtpdetails"}
*}


{capture assign=tmp}
	<table >
{call e field=html2pdf_type type=select options=[dompdf,remote,remotechrome] options_fix=1}
{call e field=html2pdf_remote_url type=text default="http://1.voro.lt:2080/html/dompdf2022/convert.php"}
{call e field=html2pdf_remotechrome_url type=text default="http://1.voro.lt:2080/apps/chromeheadless/html2pdf.php"}
	</table>
{/capture}

{call e field=html2pdf_config type=read value=$tmp rowclass="html2pdf"}


{capture assign=tmp}
	<table >

{call e field=WSS_CONTROL_USER_PREFIX type=text}
{call e field=WSS_CONTROL_USER_PASS type=password hidden_note="user:pass"}
{call e field=WSS_HOST_PORT type=text hidden_note="host:port"}
{call e field=_WSSCFG_NOTES type=text}
	</table>
{/capture}

{call e field=wss_config type=read value=$tmp rowclass="html2pdf"}



{include file="default_form_close.tpl" submit_buttons=[save]}