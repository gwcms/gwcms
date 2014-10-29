<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
	<style>
	{literal}
	body,table,td,th,tr{font-family:Tahoma,Verdana,"Trebuchet MS",Arial,Helvetica,sans-serif;font-size:12px;}
	.response_to_row td{color:#999;font-size:10px;background-color:#f9f9f9}
	th{font-family:"Trebuchet MS",Tahoma,Verdana,Arial,Helvetica,sans-serif;font-size:16px;font-weight:normal}
	table{background-color:#f5f5f5}
	td,th{background-color:white;padding:3px}
	body{background-color: #f5f5f5}
	.bulk_sms td{font-size:12px;}
	.bulk_sms_resp td{font-size:10px}
	.empty td{background-color:transparent}
	{/literal}
	</style>
</head>
<body>

<table cellspacing="1" cellpadding="0">

<tr><th>Siuntėjas</th><th>Laikas</th><th>Žinutės turinys</th></tr>

{if (count($list) > 5) && (count($list)/count($response_to) > 3)}
{*template 1 bulk sms*}
	{foreach from=$response_to item=bitem}

		<tr class="bulk_sms"><td>Atsakymai į žinutę:</td><td>{$bitem.send_time|date_format:'%Y-%m-%d'}</td><td>{$bitem.msg}</td></tr>
		{foreach from=$list item=item}
			{if $bitem.msg == $item.response_to.msg}
			<tr class="bulk_sms_resp">
				<td>{$item.from}{if $item.name} ({$item.name|escape}){/if}</td>
				<td>{$item.time}</td>
				<td>{if !$item.msg}<span style="color:silver">(Tuščia žinutė)</span>{else}{$item.msg|escape}{/if}</td>
			</tr>
			{/if}
		{/foreach}
		<tr class="empty"><td colspan="10">&nbsp;</td></tr>
	{/foreach}

{else}
{*template2 sms chat*}
	

{foreach from=$list item=item}
	
	{if $item.response_to}
		<tr class="response_to_row"><td></td><td>{$item.response_to.send_time}</td><td>{$item.response_to.msg|escape}</td></tr>
	{/if}
	<tr>
		<td>{$item.from}{if $item.name} ({$item.name|escape}){/if}</td>
		<td>{$item.time}</td>
		<td>{if !$item.msg}<span style="color:silver">(Tuščia žinutė)</span>{else}{$item.msg|escape}{/if}</td>
	</tr>

{/foreach}
</table>

{/if}
</body>
</html>