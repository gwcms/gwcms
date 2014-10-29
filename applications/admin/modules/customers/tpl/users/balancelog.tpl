{include file="default_open.tpl"}


{if $list}
<table class="gwTable gwActiveTable">
	<tr>
		<th>{$app->fh()->FieldTitle(insert_time)}</th>
		<th>{$app->fh()->FieldTitle(balance_diff)}</th>
		<th>{$app->fh()->FieldTitle(balance_diff_message)}</th>
	</tr>
			
{foreach $list as $item}
	<tr>
		<td>{$item->insert_time}</td>
		<td>{if $item->balance_diff>0}+{/if}{$item->balance_diff}</td>
		<td>{$item->msg}</td>
	</tr>
{/foreach}
</table>

{else}
	<p>{$lang.NO_ITEMS}</p>
{/if}

{include file="default_close.tpl"}