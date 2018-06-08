{include "default_open.tpl"} 



<form method="post"><input type="hidden" name="act"
	value="do:parseEmailsFromText" />

<table class="gwTable" style="width: 100%">

	<tr>
		<td style="width: 150px">Tekstas</td>
		<td>{include file="elements/inputs/textarea.tpl" input_name=string value=$string}
		</td>
	</tr>
	
	
{include file="elements/input.tpl" type=select name=lang options=$m->lang.OPT.lang empty_option=1}
{include file="elements/input.tpl" name=groups type=multiselect options=$options.groups}	
{include file="elements/input.tpl" name=active type=bool}	


{if $result}
	<tr>
		<td style="width: 150px">Rezultatas importavimui</td>
		<td>{include file="elements/inputs/textarea.tpl" value=$result}
		</td>
	</tr>	
{/if}


</table>




</small> <input type="submit" /></form>
{include "default_close.tpl"}
