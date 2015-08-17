{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=user_id type=select options=$options.user_id empty_option=1 default=$user->id}
{include file="elements/input.tpl" name=subject}
{include file="elements/input.tpl" name=message type="textarea" height="100px"}



{function name=df_submit_button_send}
	<input type="submit" value="{$m->lang.SEND}"  /> 
{/function}

{include file="default_form_close.tpl" submit_buttons=[send,cancel]}