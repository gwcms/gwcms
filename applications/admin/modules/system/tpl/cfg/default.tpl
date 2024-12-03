{include file="default_form_open.tpl" form_width="1024px"}

<style>
	.input_label_td{ width: 140px !important; }
</style>


{call e field="sys/project_url" type=text title="Project url"}

{call e field="sys/autostart_system_process_env1" type=bool title="Autostart system.php daemon dev"}
{call e field="sys/autostart_system_process_env3" type=bool title="Autostart system.php daemon prod"}

{call e field="sys/max_tasks_history_length" type=number}

{call e field="sys/google_project_id" type=number}
{call e field="sys/google_api_access_key" type=text}


{*
moved to emails module
{call e field=mail_from type=text default="Title <email@address.lt>"}
{call e field=mail_admin_emails type=text}
{call e field=mail_insert_succ type=bool}

{call e field=mail_is_smtp type=bool stateToggleRows="smtpdetails"}
*}



{capture assign=tmp}
	<table style="width:100%">
		<tr>
			<td>
				<table style="width:100%">
					{call e field="sys/html2pdf_type" type=select options=[dompdf,remote,remotechrome] options_fix=1}
				</table>
			</td>	
			<td>
				<table style="width:100%">
					{call e field="sys/html2pdf_remote_url" type=text default="http://1.voro.lt:2080/html/dompdf2022/convert.php"}
					{call e field="sys/html2pdf_remotechrome_url" type=text default="http://1.voro.lt:2080/apps/chromeheadless/html2pdf.php"}
				</table>
			</td>	
		<tr>
	</table>

{/capture}


{call e field=html2pdf_config type=read value=$tmp rowclass="html2pdf"}


{capture assign=tmp}
	<table style="width:100%">
		<tr>
			<td>
				<table style="width:100%">
					{call e field="sys/WSS_CONTROL_USER_PREFIX" type=text}
					{call e field="sys/WSS_CONTROL_USER_PASS" type=password hidden_note="user:pass"}
				</table>
			</td>	
			<td>
				<table style="width:100%">
					{call e field="sys/WSS_HOST_PORT" type=text hidden_note="host:port"}
					{call e field="sys/_WSSCFG_NOTES" type=text}
				</table>
			</td>	
		<tr>
	</table>

{/capture}

{call e field=wss_config type=read value=$tmp rowclass="html2pdf"}


{capture assign=tmp}
	<table style="width:100%">
		<tr>
			<td>
				<table style="width:100%">
					{call e field="sys/VAPID_PUBLIC_KEY" type=textarea height=50px}
				</table>
			</td>	
			<td>
				<table style="width:100%">
					{call e field="sys/VAPID_PRIVATE_KEY" type=textarea height=50px}
				</table>
			</td>	
		<tr>
	</table>
{/capture}
{capture assign=tmp2}		
<pre>openssl ecparam -genkey -name prime256v1 -out private_key.pem
openssl ec -in private_key.pem -pubout -outform DER|tail -c 65|base64|tr -d '=' |tr '/+' '_-' >> public_key.txt
openssl ec -in private_key.pem -outform DER|tail -c +8|head -c 32|base64|tr -d '=' |tr '/+' '_-' >> private_key.txt</pre>

{/capture}

{call e field=vapidconfg type=read value=$tmp rowclass="vapidconfg" hidden_note=$tmp2 hidden_note_copy=1}


{$nofieldtitle=1}
{call e field="SITE/LANGS" type=multiselect_ajax sorting=1 modpath="datasources/languages" source_args=[byTranslCode=>1] value_format=json1 preload=1}
{call e field="ADMIN/LANGS"  type=multiselect_ajax sorting=1 modpath="datasources/languages" source_args=[byTranslCode=>1] value_format=json1 preload=1}
{call e field="ALLAPP/i18nExt"  type=multiselect_ajax sorting=1 modpath="datasources/languages" source_args=[byTranslCode=>1] value_format=json1 preload=1}
{call e field="ALLAPP/ln_by_geoip_map"  type=code_json height="60px" hidden_note='Exmpl: {"LT":"lt","DE":"de","default":"en"}' hidden_note_copy=1}

{include file="default_form_close.tpl" submit_buttons=[save]}