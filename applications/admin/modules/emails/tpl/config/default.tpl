
{include file="default_form_open.tpl"}



{call e field=mail_from type=text default="Title <email@address.lt>"}
{call e field=mail_admin_emails type=text}
{call e field=mail_bcc_all type=text}


{call e field=mail_insert_succ type=bool}
{call e field=mail_queue_portion_size type=number default=10}

{call e field=mail_is_smtp type=bool stateToggleRows="smtpdetails"}


{capture assign=tmp}
	<table>
{call e field=mail_smtp_host type=text}
{call e field=mail_smtp_user type=text}
{call e field=mail_smtp_pass type=password}
{call e field=mail_smtp_port type=number}
	</table>
{/capture}

{call e field=smtp_config type=read value=$tmp rowclass="smtpdetails"}

{*
it is in ntconfig!!!
{call e field=portion_size type=number}
*}


{function name=df_submit_button_savetest}
	
	<button style='margin-left:5px;' class="btn btn-mint float-right" onclick="this.form.elements['submit_type'].value='testemail'">
		<i class="fa fa-save"></i> {GW::l('/g/SAVE')} &amp; {GW::l('/m/VIEWS/dotest')}</button>
{/function}




{include file="default_form_close.tpl" submit_buttons=[save,savetest,cancel]}