{include "default_open.tpl"}



<table class="gwTable">
	<tr>
		<th>{$app->fh()->FieldTitle(title)}</th>
		<td>{$item->title}</td>
	</tr>

	<tr>
		<th>{$app->fh()->FieldTitle(subject)}</th>
		<td>{$item->subject}</td>
	</tr>
</table>

<br />
	
<table class="gwTable">
	<tr>
		<th>{$app->fh()->FieldTitle(row_num)}</th>
		<th>{$app->fh()->FieldTitle(title)}</th>
		<th>{$app->fh()->FieldTitle(email)}</th>
		<th>{$app->fh()->FieldTitle(time)}</th>
		<th>{$app->fh()->FieldTitle(sent)}</th>
	</tr>
	

	{$row=1}

{foreach $item->sent_info as $iitem}
	<tr>
		<td>{$row}{$row=$row+1}</td>
		<td>{$iitem->name|escape} {$iitem->surname|escape}</td>
		<td>
			{*mailinator check testin*}
			{if strpos($iitem->email,'@mailinator.com')}
				<a href='http://mailinator.com/inbox.jsp?to={str_replace('@mailinator.com','',$iitem->email)}' target='_blank'>{$iitem->email}</a>
			{else}
				{$iitem->email}
			{/if}
		</td>
		<td>{$iitem->time}</td>
		<td>{if $iitem->sent}
			<span style="color:green">{$lang.YES}</span>
		{else}
			<span style="color:red">{$lang.NO}</span>
		{/if}</td>
	</tr>
{/foreach}
</table>

{include "default_close.tpl"}