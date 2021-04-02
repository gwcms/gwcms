{assign var=form_width value="900px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}


{$fields_config=[
	fields=>[
		title=>[type=>text],
		email=>[type=>text],
		active=>[type=>bool],
		serv_countries=>[type=>multiselect_ajax, modpath=>"datasources/countries", preload=>1,options=>[],value_format=>json1 ]
	]
]
}


{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}