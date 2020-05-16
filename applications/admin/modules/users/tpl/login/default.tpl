{include "default_open.tpl"}


<form method="POST" id="lgn_frm" action="{$app->uri}"><input
	type="hidden" name="act" value="do_login" />

<table class="login_frm">
	<tr>
		<td style="padding-right: 10px">{$lang.USER}</td>
		<td><input class="lgn_inpt_vart{if $login_error}err{/if}"
			name="login[0]" value="{if isset($smarty.cookies.login_0)}{$smarty.cookies.login_0}{/if}" /></td>
	</tr>
	<tr>
		<td style="padding-right: 10px">{$lang.PASS}</td>
		<td><input class="lgn_inpt_pwd{if $login_error}err{/if}"
			name="login[1]" type="password" /></td>
	</tr>

	{if $autologin}
	<tr>
		<td style="padding-right: 10px">{$lang.AUTOLOGIN}</td>
		<td><input name="login_auto" type="checkbox" /></td>
	</tr>
	{/if} {if count(GW::$settings.ADMIN.LANGS)>1}
	<tr>
		<td>{$lang.LANGUAGE}:</td>
		<td><select name="ln">
			{foreach GW::$settings.ADMIN.LANGS as $ln_code}
			<option value="{$ln_code}"
				{if isset($smarty.cookies.login_ln) && $smarty.cookies.login_ln==$ln_code}SELECTED{/if}>
			{$lang.LANG.$ln_code}</option>
			{/foreach}
		</select></td>
	</tr>
	{/if}

	<tr>
		<td></td>
		<td><input class="submit_btn" type="submit"
			value="{$lang.DOLOGIN}" /></td>
	</tr>


</table>
</form>
	
<script type="text/javascript">
	$('input[name=login[0]]').focus();
</script> {include file="default_close.tpl"}