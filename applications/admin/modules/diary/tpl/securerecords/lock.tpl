{include file="default_form_open.tpl" form_width="600px" changes_track=1 action=encrypt}

{*$nowrap=1*}


{call e field=encryptkey type=password}
{call e field=encryptkey_repeat type=password}


<input type='hidden' name='id' value='{$smarty.get.id}'>
<input type="hidden" name="encrypt_1_decrypt_0" value="1" />

{function name=df_submit_button_encrypt}
	<button class="btn btn-primary"><i class="fa fa-lock"></i> Užrakint</button>
{/function}




{include file="default_form_close.tpl" submit_buttons=[encrypt]}
