{if !$input_name_pattern}
	{$input_name_pattern="item[%s]"}
	{if $type=='multiselect'}{$input_name_pattern="`$input_name_pattern`[]"}{/if}	
{/if}
{$input_name=$input_name_pattern|sprintf:$name}
{$title=$title|default:FH::fieldTitle($name)}

{if !$value}
	{$value=$item->get($name)|default:$default}
{/if}

{$inp_type=$type|default:'text'}

{if $type=='password'}{$inp_type='text'}{/if}

{include file="elements/inputs/`$inp_type`.tpl"}
