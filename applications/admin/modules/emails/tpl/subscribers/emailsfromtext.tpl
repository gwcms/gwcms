{include file="default_form_open.tpl" action="parseEmailsFromText"}

</table>


<table class="gwTable" style="width: 100%">

	<tr>
		<td style="width: 150px">Tekstas</td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=string value=$string}
		</td>
	</tr>
	
	
{call e field=lang type=select options=$m->lang.OPT.lang empty_option=1}
{call e field=groups type=multiselect options=$options.groups}	
{call e field=active type=bool}	


{if $result}
	<tr>
		<td style="width: 150px">Rezultatas importavimui</td>
		<td>{include file="elements/inputs/textarea.tpl" value=$result}
		</td>
	</tr>	
{/if}


{function name=df_submit_button_send}
	<button class="btn btn-primary"><i class="fa fa-save"></i> {$m->lang.SEND}</button>
{/function}



{include file="default_form_close.tpl" submit_buttons=[submit,cancel]}


