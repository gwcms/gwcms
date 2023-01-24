{*assign var=form_width value="100%" scope=global*}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}



{$fields_config=[
	cols=>1,
	fields=>[
		obj_type=>[type=>text],
		unit_price=>[type=>number,step=>"0.01"],
		qty=>[type=>number],
		link=>[type=>text],
		processed=>[type=>bool],
		vat_group=>[type=>select_ajax,options=>[],modpath=>'payments/vatgroups',empty_option=>1]
	]

]}

{if $smarty.get.shift_key || !$item->obj_type}
	{$fields_config.fields.obj_type=[type=>select, empty_option=>1, options_fix=>1, options=>$m->options.obj_type]}
	{$fields_config.fields.obj_id=[type=>text]}
	
	{$fields_config.fields.context_obj_type=[type=>select, empty_option=>1, options_fix=>1, options=>$m->options.context_obj_type]}
	{$fields_config.fields.context_obj_id=[type=>text]}
	
	{$fields_config.fields.deliverable=[type=>bool]}
{/if}


{*unit_price=>[type=>number,step=>0.01],*}


{if $item->obj_type && $item->modpath}
	{$fields_config.fields.obj_id=[type=>select_ajax,modpath=>$item->modpath, preload=>1,options=>[],source_args=>[addcontext=>1]]}
{/if}

{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}





<style>
	.input_label_td{ width: 120px !important; }
	.input_td{ width: 300px; }
</style>