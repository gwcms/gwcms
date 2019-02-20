{include file="default_form_open.tpl"}

{$width_input="70%"}

{call e field=user_id type=select options=$options.user_id empty_option=1 default=$user->id}
{call e field=subject}
{call e field=message type="textarea" height="100px"}

{call e field=level type=number}


{function name=df_submit_button_send}
	<button class="btn btn-primary"><i class="fa fa-save"></i> {$m->lang.SEND}</button>
{/function}



{include file="default_form_close.tpl" submit_buttons=[send,cancel]}