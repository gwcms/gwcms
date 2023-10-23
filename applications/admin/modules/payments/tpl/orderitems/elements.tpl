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
		vat_group=>[type=>select_ajax,options=>[],modpath=>'payments/vatgroups',empty_option=>1,preload=>1]
	]

]}

{if $smarty.get.shift_key || !$item->obj_type}
	{$fields_config.fields.obj_type=[type=>select, empty_option=>1, options_fix=>1, options=>$m->options.obj_type]}
	{$fields_config.fields.obj_id=[type=>text]}
	
	{$fields_config.fields.context_obj_type=[type=>select, empty_option=>1, options_fix=>1, options=>$m->options.context_obj_type]}
	{$fields_config.fields.context_obj_id=[type=>text]}
	
	{$fields_config.fields.deliverable=[type=>bool]}
	
{/if}

{if $smarty.get.shift_key && $app->user->isRoot()}
	{$fields_config.fields.group_id=[type=>text]}
{/if}


{*unit_price=>[type=>number,step=>0.01],*}


{if $item->obj_type && $item->modpath}
	{$fields_config.fields.obj_id=[type=>select_ajax,modpath=>$item->modpath, preload=>1,options=>[],source_args=>[addcontext=>1]]}
{/if}


{*dinamiskai pagal projekta dadedami*}
{foreach $m->mod_fields as $input}
	{if $input->type==optional && !isset($dynfields[$input->fieldname])}
		{continue}
	{/if}
	
	{$field=[
		field=>$input->get(fieldname),
		type=>$input->get(inp_type),
		note=>$input->get(note),
		title=>$input->get(title),
		placeholder=>$input->placeholder,
		hidden_note=>$input->hidden_note,
		i18n=>$input->get(i18n),
		colspan=>1
	]}
	{if $input->type==extended}
		{$field.field="keyval/{$input->get(fieldname)}"}
	{/if}
	
	{$opts=$input->get('config')}		
	{if $input->get(inp_type)=='select_ajax'}
		{$opts.preload=1}
		{$opts.modpath=$input->get('modpath')}
		{$opts.after_input_f=editadd}
	{/if}	
	{if is_array($opts)}
		{$field = array_merge($field, $opts)}
	{/if}
	
	{$fields_config.fields[$input->get(fieldname)] = $field}
	
{/foreach}


{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}





<style>
	.input_label_td{ width: 120px !important; }
	.input_td{ width: 300px; }
</style>