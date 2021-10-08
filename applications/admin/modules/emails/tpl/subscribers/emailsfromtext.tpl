{include file="default_form_open.tpl" action="parseEmailsFromText"}

</table>


<table class="gwTable" style="width: 100%">

	<tr>
		<td style="width: 150px">Tekstas</td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=string value=$string}
		</td>
	</tr>
	
	
{call e field=lang type=select options=GW::l('/m/OPT/lang') empty_option=1}
{call e field=groups type=select_ajax modpath="emails/groups" preload=1 options=[] after_input_f="editadd"}	
{call e field=active type=bool}	


{*
{if $result}
	<tr>
		<td style="width: 150px">Rezultatas importavimui</td>
		<td>{include file="elements/inputs/textarea.tpl" value=$result}
		</td>
	</tr>	
{/if}
*}

{function name=df_submit_button_send}
	<button class="btn btn-primary"><i class="fa fa-save"></i> {GW::l('/m/SEND')}</button>
{/function}



{include file="default_form_close.tpl" submit_buttons=[submit,cancel]}


