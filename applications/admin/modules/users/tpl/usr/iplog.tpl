{include file="default_open.tpl"}


<table class="gwTable">
	<tr>
		<th>{$app->fh()->fieldTitle(insert_time)}</th>
		<th>{$app->fh()->fieldTitle(ip)}</th>
		<th>{$app->fh()->fieldTitle(user_agent)}</th>
	</tr>
{foreach $list as $item}
	<tr>
		<td>{$item->insert_time}</td>
		<td>{$item->ip}</td>
		<td>{$item->user_agent}</td>
	</tr>
{/foreach}
</table>

{include file="default_close.tpl"}