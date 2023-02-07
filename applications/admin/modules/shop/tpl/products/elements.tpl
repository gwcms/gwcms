{function name=df_submit_button_saveAndItax}
	<button class="btn btn-warning float-rights" onclick="this.form.elements['submit_type'].value=7;"><i class="fa fa-floppy-o"></i> {GW::l('/g/SAVE')} + Itax <i class="fa fa-retweet"></i> </button>
{/function}

	
{*
outbound_flight_id
outbound_details
inbound_flight_id
inbound_flight_details
*}



{assign var=form_width value="1200px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}




{$sel_ajax=[options=>[], preload=>1, after_input_f=>editadd, type=>select_ajax]}


{*[type=>select, options=>[-1=>'not processed','0'=>'empty info','1'=>'has info']],*}
{*attachments=>[type=>attachments, valid=>[image=>[storewh=>'1500x1500',minwh=>'1x1',maxwh=>'6000x6000'],limit=>5], preview=>[thumb=>'50x50'], colspan=>1],*}

{$fields_config=[
	cols=>2,
	fields=>[
		image=>[type=>image, colspan=>1],
		"keyval/description"=>[type=>htmlarea,colspan=>1],
		title => [type=>text, colspan=>1], 
		type => [modpath=>'shop/prodtypes', colspan=>1, 
		note=>GW::l('/m/UPDATE_AFTER_CHANGE'), hidden_note=>GW::l('/m/UPDATE_AFTER_CHANGE_HIDD')]+$sel_ajax,
		active => [type=>bool, colspan=>1],
		oldprice => [type=>number, colspan=>1,step=>0.01],
		price => [type=>number, colspan=>1,step=>0.01],
		qty => [type=>number, colspan=>1],
		weight => [type=>number,step=>0.001, colspan=>1],
		priority => [type=>number, colspan=>1]
	]
]
}


{if $m->feat(vatgroups) || $item->vat_group}

	{$fields_config.fields.vat_group=[modpath=>'payments/vatgroups', colspan=>1, empty_option=>1, hidden_note=>"leave empty to use shop default",options=>[], preload=>1, type=>select_ajax]}
{/if}



{*
{*price_scheme => [type=>text,colspan=>1],*}
{*modif_title=>[type=>text, colspan=>1],*}
{if $item->parent_id}
	{$mod_fields=[modif_title=>[type=>text, colspan=>1]]}
	{$fields_config.fields = $mod_fields + $fields_config.fields}
	{gw_unassign var=$fields_config.fields.title}
{/if}


{$classmap=$m->lang.OPTIONS.class_types_map}

{$dynamic_fields=[
	vendor=>[modpath=>'shop/classificators',source_args=>[type=>$classmap.vendor], colspan=>1]+$sel_ajax
]}

{if $item->type}
	{$dynfields = array_flip((array)$item->typeObj->fields)}
{/if}


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
