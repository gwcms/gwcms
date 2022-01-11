{if is_array($value) || is_object($value)}
	{$value=GW_Json_Format_Helper::f($value)}
{elseif is_string($value)}
	
	{$arr=json_decode($value)}

	{if $arr} {*if json is valid - humanize json text*}
		{$value = GW_Json_Format_Helper::f($arr)}
	{/if}
{/if}

{include file="{$smarty.current_dir}/code.tpl" codelang=json}
