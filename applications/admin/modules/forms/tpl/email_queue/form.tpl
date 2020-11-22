{include file="default_form_open.tpl" form_width="100%"}
{$width_title="150px"}

{$f="elements/input.tpl"}





{call e field=from hidden_note=GW::l('/m/email_note') default=$m->config->default_sender}
{call e field=to hidden_note=GW::l('/m/email_note')  default=$m->config->default_replyto}
{call e field=subject}

{if $item->plain}
	{call e field=body type=textarea height=100px}
{else}
	{call e field=body type=htmlarea layout=wide}
{/if}



{function name=df_submit_button_send}
	<button class="btn btn-warning float-rights" onclick="this.form.elements['submit_type'].value=7;"><i class="fa fa-floppy-o"></i> {GW::l('/g/SAVE')} + {GW::l('/m/SEND')} <i class="fa fa-paper-plane-o"></i></button>
{/function}

{if $item->status != "SENT"}
	{$submit_buttons=[save,send]}
{else}
	{$submit_buttons=[save,apply,cancel]}
{/if}


{include file="default_form_close.tpl" extra_fields=false}
	