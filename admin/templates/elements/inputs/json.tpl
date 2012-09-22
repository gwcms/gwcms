{*

Edit array in json format
Edit json in human readable format

*}
{if is_array($value)}
	{$value=GW_Json_Format_Helper::f($value)}
{elseif is_string($value)}
	
	{$arr=json_decode($value)}

	{if $arr} {*if json is valid - humanize json text*}
		{$value = GW_Json_Format_Helper::f($arr)}
	{/if}
{/if}

{include file="elements/inputs/textarea.tpl"}