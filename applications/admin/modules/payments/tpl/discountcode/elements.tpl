{assign var=form_width value="800px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}


{$fields_config=[
	fields=>[
		code=>[type=>text,required=>1],
		percent=>[type=>number],
		limit_amount=>[type=>number,step=>0.01],
		note=>[required=>1],
		active=>[type=>bool],
		singleuse=>[type=>bool],
		obj_type=>[type=>select, options=>$item->getTypes(), empty_option=>1, options_fix=>1],
		valid_from=>[type=>date],
		expires=>[type=>date,title=>GW::l('/m/FIELDS/discount_expires')],
		user_id=>['type'=>'select_ajax', 'options'=>[], 'preload'=>1,'modpath'=>'customers/users', empty_option=>1, 'hidden_note'=>'Parinkite vartotoją kuriam ši nuolaida būtu prieinama']
	]
]
}



{if $item->obj_type == 'shop_products'}
	{$fields_config.fields.products=[type=>multiselect_ajax, modpath=>"shop/products", preload=>1,options=>[],value_format=>json1 ]}
{elseif $item->obj_type == 'nat_products'}
	{$fields_config.fields.products=[type=>multiselect_ajax, modpath=>"products/items", preload=>1,options=>[],value_format=>json1 ]}
{/if}


{if $smarty.get.shift_key==1 && $app->user->isRoot()}
	{$fields_config.fields.used_amount=[type=>text]}
{else}
	{$fields_config.fields.used_amount=[type=>read]}
	{$fields_config.fields.used=[type=>read]}
	{*
	{$fields_config.fields.user_id.readonly=true}
	*}
{/if}



{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}