{include "default_open.tpl"}

<div id="cart">
{if $smarty.get.success}
	{$m->lang.USER_REGISTER_SUCCESS}
	<a href="">{$m->lang.LOGIN}</a>
{else}

{$page->getContent('top_text')}


<small><em>{$lang.ASTERISK_REQUIRED_FIELDS}</em></small>

{function input}
	<tr>
		<td {if $m->error_fields.$field}class="error_cell"{/if}>{$m->lang.FIELDS.$field} {if $required}*{/if}</td>
		<td><input {if $type=="password"}type="password"{else}type="text"{/if} name="item[{$field}]" value="{$item->$field|escape}"></td>
		<td class="error_cell">
			{if $m->error_fields.$field}
				{GW_Error_Message::read($m->error_fields.$field)}
			{/if}
		</td>
	</tr>
{/function}


<form action="{$smarty.server.REQUEST_URI}" method="post" class="user_register">
<input type="hidden" name="act" value="do:register" />

<table>
	{input field="email" required=1}
	{input field="pass_new" required=1 type="password"}
	{input field="pass_new_repeat" required=1 type="password"}	
	{input field="first_name" required=1}	
	{input field="second_name" required=1}	
	{input field="phone" required=1}	
	{input field="company_name" required=0}


	<tr><td></td><td><input id="buylink" type="submit" value="Siųsti"></td></tr>

</table>

</form>

{/if}
</div>

<br /><br />


{include "default_close.tpl"}