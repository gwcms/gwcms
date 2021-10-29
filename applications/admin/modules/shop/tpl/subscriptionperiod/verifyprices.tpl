{include file="default_form_open.tpl" form_width="1040px" action=VerifyPrices} 



{call e field=spreadsheet_copypaste type=textarea layout=wide}

{call e field=col_nr_is_productid type=select options=[1=>1,2=>2,3=>3,4=>4,5=>5]}


{function name=df_submit_button_verify}
	<button class="btn btn-mint">Patikrinti </button>
{/function}

{include file="default_form_close.tpl" submit_buttons=[verify,cancel]}

{include file="default_form_close.tpl"}
