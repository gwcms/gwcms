
{include file="default_form_open.tpl" form_width="600px" changes_track=1 action=showone}

{*$nowrap=1*}


{call e field=encryptkey type=password}




{function name=df_submit_button_dencrypt}
	<button class="btn btn-default"><i class="fa fa-unlock"></i> Atrakint</button>
{/function}


{include file="default_form_close.tpl" submit_buttons=[dencrypt]}
