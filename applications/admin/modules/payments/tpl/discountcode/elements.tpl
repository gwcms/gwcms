{assign var=form_width value="800px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}


{$fields_config=[
	fields=>[
		code=>[type=>text],
		percent=>[type=>number],
		limit_amount=>[type=>number,step=>0.01],
		note=>[],
		active=>[type=>bool],
		singleuse=>[type=>bool],
		obj_type=>[type=>select, options=>$item->getTypes(), empty_option=>1, options_fix=>1],
		valid_from=>[type=>date],
		expires=>[type=>date,title=>GW::l('/m/FIELDS/discount_expires')],
		products=>[type=>multiselect_ajax, modpath=>"shop/products", preload=>1,options=>[],value_format=>json1 ]
	]
]
}

{if $smarty.get.shift_key==1 && $app->user->isRoot()}
	{$fields_config.fields.used_amount=[type=>text]}
{/if}



{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}