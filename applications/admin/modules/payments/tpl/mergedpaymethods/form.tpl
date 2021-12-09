{include file="default_form_open.tpl"}


	

{$fields=[
	["gateway"]=>[readonly=>1],
	["country"]=>[readonly=>1],
	["key"]=>[readonly=>1],
	["group"]=>[readonly=>1],
	["title"]=>[readonly=>1],
	["logo"]=>[readonly=>1],
	["min_amount"]=>[readonly=>1],
	["max_amount"]=>[readonly=>1],
	["insert_time"]=>[readonly=>1],
	["update_time"]=>[readonly=>1]
]
}

{if $item->gateway=='montonio'}
	{$fields.group.readonly=1}
	{$fields.min_amount.readonly=1}
	{$fields.max_amount.readonly=1}
{/if}


{foreach $fields as $field => $input}
	{call e params_expand=$input}
{/foreach}

{call e field=aliaskey}

{include file="default_form_close.tpl"}