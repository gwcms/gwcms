{include "default_open.tpl"}
{include "messages.tpl"}

<center>
<table class="did">

<tr>
<td valign="top">

<fieldset>
<legend>Registruotiems vartotojams</legend>

	<form action="{$smarty.server.REQUEST_URI}" method="post">
		<input type="hidden" name="act" value="do:login" />
		
		{if $errors}
			<p>{$errors}</p>
		{/if}

		<table >
		<tr>
			<td class="txt">Prisijungimo vardas:</td>
			<td class="fld"><input name="login[0]" value="{$login->name}" /></td>
		</tr>
		
		<tr>
			<td class="txt">Prisijungimo slaptažodis:</td>
			<td class="fld"><input name="login[1]" value="{$login->pass}" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input id="buylink"  type="submit" value="Prisijungti"></td>
		</tr>
		</table>

	</form>

</fieldset>

</td>
<td valign="top">


<fieldset>
<legend>Naujiems vartotojams</legend>

<form action="{$ln}/{$page->path}/register">
	<input id="buylink"  type="submit" value="Registruotis">
</form>

</fieldset>



</fieldset>

{*-----------------------------PASSCHANGE-------------------------------------*}
<fieldset>
<legend>Pamiršote slaptažodį</legend>

	{if !$smarty.get.passchange}
		<input id="buylink" type="submit" value="Slaptažodžio priminimas" onclick="$(this).hide();$('#passchange').fadeIn();" />
	{/if}
	
<form 
	action="{$ln}/{$page->path}?passchange=1" 
	method="post" 
	id="passchange" 
	{if !$smarty.get.passchange}style="display:none"{/if}>
	<input type="hidden" name="act" value="do:passchange" />
	<table>
		<tr>
			<td>El. pašto adresas</td>
			<td>
				<input 
					{if $smarty.get.passchange}class="error_field"{/if} 
					name="email" 
					value="{$smarty.post.email|escape}" 
					placeholder="@" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Siųsti"></td>		
		</tr>
	</table>
</form>

</fieldset>
{*-----------------------------PASSCHANGE-------------------------------------*}


</td>

</tr>
</table>
</center>

{include "default_close.tpl"}