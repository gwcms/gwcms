

{call e field="default_country"
	type="select_ajax"
	after_input_f="editadd"
	modpath="datasources/countries"
	source_args=[byCode=>1]
	options=[]
	preload=1
	i18n=4
	i18n_expand=1
}

{*
{call e field="priority_gateway" type=multiselect_ajax sorting=1 options=$m->options.gateway value_format=json1 options_fix=1}
{call e field="priority_group" type=multiselect_ajax sorting=1 options=$m->options.group value_format=json1  options_fix=1}
*}
{call e field="disabled_group" type=multiselect_ajax options=$m->options.group  value_format=json1  options_fix=1}

{call e field=all_countries type="bool" hidden_note="some countries might not have any options but you might want to list them, so call OTH - other country options"}

