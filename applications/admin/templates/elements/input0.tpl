


{if !$input_name_pattern}
	{$input_name_pattern="item[%s]"}
	{if $type=='multiselect'}{$input_name_pattern="`$input_name_pattern`[]"}{/if}
{/if}
{$input_name=$input_name_pattern|sprintf:$name}

{if !$id}
	{$id=str_replace(["[","]"],'_',$input_name)}
{/if}


{if !$value}
	{$value=$item->get($name)}
		

	{if $value!=='0' && !$value && $default}
		{$value=$default}
	{/if}
{/if}

{if is_array($value)}
	{$value=GW_Json_Format_Helper::f($value)}
{/if}






{$inp_type=$type|default:'text'}

{if $type=='password'}{$inp_type='text'}{/if}
{include file="elements/inputs/`$inp_type`.tpl"}  

