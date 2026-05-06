{include file="default_form_open.tpl" action=pushmessage nohiddenitemid=1}

{$width_input="70%"}

<input type="hidden" name="item[user_id]" value="{$user->id}" />
<input type="hidden" name="item[level]" value="15" />

{call e field=subject}
{call e field=message type="textarea" height="120px"}

{function name=df_submit_button_send}
	<button class="btn btn-warning"><i class="fa fa-bell"></i> Siųsti push</button>
{/function}

{include file="default_form_close.tpl" submit_buttons=[send,cancel]}
