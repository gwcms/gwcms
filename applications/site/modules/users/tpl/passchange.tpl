{include "default_open.tpl"}
{include "messages.tpl"}


<form action="{$smarty.server.REQUEST_URI}" method="post" id="passchange_new">
	<input type="hidden" name="act" value="do:PassChange2">
	<table>
		<tr>
			<td>Naujas slaptažodis</td>
			<td><input type="password" name="login_id[]" /></td>	
		<tr>
		<tr>
			<td>Pakartokite naują slaptažodį</td>
			<td><input type="password" name="login_id[]" /></td>	
		<tr>
		
		<tr>
			<td></td>
			<td><input type="submit" /></td>	
		<tr>
			
				
	</table>
	
	
</form>

{include "default_close.tpl"}