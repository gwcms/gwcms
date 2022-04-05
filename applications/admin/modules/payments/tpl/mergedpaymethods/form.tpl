{include file="default_form_open.tpl"}



{$fields=[
	gateway=>[],
	country=>[],
	key=>[],
	group=>[],
	title=>[],
	logo=>[],
	min_amount=>[],
	max_amount=>[],
	priority=>[],
	insert_time=>[],
	update_time=>[]
]
}

{if $item->gateway==montonio || $item->gateway==paysera}	
	{$fields.gateway.readonly=1}	
	{$fields.country.readonly=1}	
	{$fields.key.readonly=1}	
	{$fields.group.readonly=1}	
	{$fields.title.readonly=1}	
	{$fields.logo.readonly=1}	
	{$fields.min_amount.readonly=1}	
	{$fields.max_amount.readonly=1}	
{/if}

{if $item->gateway=='montonio'}
	{$fields.group.readonly=0}
	{$fields.min_amount.readonly=0}
	{$fields.max_amount.readonly=0}
{/if}


{foreach $fields as $field => $input}
	{call e params_expand=$input}
{/foreach}

{call e field=aliaskey}

{include file="default_form_close.tpl"}