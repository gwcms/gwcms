{include "default_open.tpl"}

<table>
	<tr>
		<th>Žinutė</th>
		<th>Gavėjų kiekis</th>
		<th>Tipas</th>
		<th>Dalių sk.</th>
		<th>Būklė</th>
		<th>Siuntėjas</th>
		<th>Atidėjimo laikas</th>
	</tr>
{foreach $list as $item}
	<tr>
		<td>{$item->message|truncate:80}</td>
		<td>{$item->recipients_count}</td>
		<td>{if $item->encoding==16}70{else}160{/if} simb.</td>
		<td>{$item->parts_count}</td>
		<td>{$m->lang.status_opt[$item->status]}</td>
		<td>{$item->sender}</td>
		<td>{if $item->send_time!='0000-00-00 00:00:00'}{$item->send_time}{/if}</td>
	</tr>
		
{/foreach}
</table>

{include "default_close.tpl"}