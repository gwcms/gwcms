{include file="default_form_open.tpl" form_width="600px" changes_track=1 action=encrypt}

{*$nowrap=1*}


{include file="elements/input.tpl" name=encryptkey type=password}




<input type="hidden" name="encrypt_1_decrypt_0" value="0" />

{function name=df_submit_button_encrypt}
	<button class="btn btn-primary" onclick="this.form.elements['encrypt_1_decrypt_0'].value=1;"><i class="fa fa-lock"></i> Užrakint</button>
{/function}

{function name=df_submit_button_dencrypt}
	<button class="btn btn-default" onclick="this.form.elements['encrypt_1_decrypt_0'].value=0;"><i class="fa fa-unlock"></i> Atrakint</button>
{/function}


{include file="default_form_close.tpl" submit_buttons=[encrypt,dencrypt]}
