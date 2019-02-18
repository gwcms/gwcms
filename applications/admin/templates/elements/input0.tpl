{*
DEPRECATED 2019-02
use {call e0 field=
instead of {include "elements/input0.tpl" name=
*}

{if $params_expand}
	{foreach $params_expand as $k => $v}
		{assign var=$k value=$v}
	{/foreach}
{/if}

{if !$input_name_pattern}
	{$input_name_pattern="item[%s]"}
	{if strpos($type, 'multiselect')!==false}{$input_name_pattern="`$input_name_pattern`[]"}{/if}
{/if}
{$input_name=$input_name_pattern|sprintf:$name}

{if !$id}
	{$id=str_replace(["[","]"],'__',$input_name)}
	{$id=str_replace("/",'___',$id)}
{/if}

{assign var="input_id" value=$id scope=parent}

{if !$value}
	
	{if strpos($name, '/')!==false}
		{$value=$item->get($name)}
	{else}	
		{$value=$item->$name}
	{/if}
	


	{if $value!=='0' && !$value && $default}
		{$value=$default}
	{/if}
{/if}

{if is_array($value) && $type=='textarea'}
	{$value=GW_Json_Format_Helper::f($value)}
{/if}

{if $options_fix}
	{$tmp=[]}
	{foreach $options as $opt}
		{$tmp[$opt]=$opt}
	{/foreach}
	{$options=$tmp}
{/if}



	{$inp_type=$type|default:'text'}

	{if $type=='password'}{$inp_type='text'}{/if}
	{include file="elements/inputs/`$inp_type`.tpl"}
	
	
{if !in_array($type,["read",'image','attachments','file']) && $readonly != 1}
	{$tmppattern = str_replace('item[','fields[', $input_name_pattern)}
	<input name="{$tmppattern|sprintf:$name}" type="hidden" value="1" />
{/if}