{include file="default_form_open.tpl" action=setpw}


{call e field=name}
{call e field=surname}

{if !$decoded}
	
	{call e field=pw}
	{call e field=valid type=select options=['300'=>'5min','900'=>'15min','1800'=>'30min','3600'=>'60min']}
	{$submit_buttons=[decrypt]}
{else}
	{$submit_buttons=false}
{/if}

{call e field=num_cvc_exp}


{function name=df_submit_button_decrypt}
	<button class="btn btn-default"><i class="fa fa-unlock"></i> Atrakint</button>
{/function}

{include file="default_form_close.tpl"}
