
{include file="default_form_open.tpl"}



{include file="elements/input.tpl" name=mail_from type=text default="Title <email@address.lt>"}
{include file="elements/input.tpl" name=mail_admin_emails type=text}
{include file="elements/input.tpl" name=mail_bcc_all type=text}


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




{function name=df_submit_button_savetest}
	
	<button style='margin-left:5px;' class="btn btn-mint float-right" onclick="this.form.elements['submit_type'].value='testemail'">
		<i class="fa fa-save"></i> {$lang.SAVE} &amp; {GW::l('/m/VIEWS/dotest')}</button>
{/function}




{include file="default_form_close.tpl" submit_buttons=[save,savetest,cancel]}