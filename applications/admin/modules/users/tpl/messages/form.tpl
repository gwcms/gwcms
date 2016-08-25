{include file="default_form_open.tpl"}

{$width_input="70%"}

{include file="elements/input.tpl" name=user_id type=select options=$options.user_id empty_option=1 default=$user->id}
{include file="elements/input.tpl" name=subject}
{include file="elements/input.tpl" name=message type="textarea" height="100px"}

{include file="elements/input.tpl" name=level type=number}


{function name=df_submit_button_send}
	<button class="btn btn-primary"><i class="fa fa-save"></i> {$m->lang.SEND}</button>
{/function}



{include file="default_form_close.tpl" submit_buttons=[send,cancel]}