<div id="login_dialog" title="{$lang.LOGIN_TO_EXTEND_SESSION}" button_ok="{$lang.CONTINUE}" button_cancel="{$lang.CANCEL}">


<table width="100%" height="100%"><tr><td align="center">


{if $success}
	<br />
	<div style="color:green">{$lang.SESSION_EXTEND_SUCCESS}</div>
	<script type="text/javascript">gw_login_dialog.success();</script>
{else}


<div class="loading-switch">
{include file="messages.tpl"}

<table class="login_frm">
	<tr>
		<td  style="padding-right:10px">{$lang.USER}</td>
		<td><input class="lgn_inpt_vart{if $login_error}err{/if}" name="login[0]" value="{$smarty.cookies.login_0}" /></td>
	</tr>
	<tr>
		<td style="padding-right:10px">{$lang.PASS}</td>
		<td><input class="lgn_inpt_pwd{if $login_error}err{/if}" name="login[1]" type="password" /></td>
	</tr>
</table>
</div>

<img class="loading-switch" style="display:none" src="{$app_root}img/loading64.gif" />

{/if}

</td></tr></table>

</div>



