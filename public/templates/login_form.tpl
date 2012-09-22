<form method="post" id="lgn_frm" action="">
	<input type="hidden" name="pre_act" value="do_login" />
	<table width="370" border="0" cellspacing="0">
		<tr>
			<td width="50px"><label style="float:left;" for="username">E-post:</label></td>
			<td width="150px"><input id="username" name="ulogin[0]" value="{$smarty.cookies.ulogin_0}" title="username" tabindex="4" type="text"></td>
			<td><a href="{$request->ln}/registrer" id="ny_brukerk">Registrer ny bruker</a></td>
		</tr>
		<tr>
			<td width="50px"><label style="float:left;" for="password">Passord:</label></td>
			<td width="150px"><input id="password" name="ulogin[1]" value="" title="password" tabindex="5" type="password"></td>
			<td><a href="#" id="resend_password_link">Glemt passordet?</a></td>
		</tr>
		<tr>
			<td colspan="2" width="200px" align="right"><input id="signin_submit" value="Logg inn" tabindex="6" type="submit">
			</td>
			<td><input id="remember" name="ulogin_auto" value="1" tabindex="7" type="checkbox"><label for="remember">Husk meg</label>
			</td>
			<td></td>
		</tr>
	</table>     
</form>