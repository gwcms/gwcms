

{assign var=form_width value="900px" scope=global}

{assign var=width_title value="140px" scope=global}
{assign var=labelright value=1 scope=global}


{$fields_config=[
	fields=>[
		aliaskey=>[type=>text]
	]
]
}


{include "tools/form_components.tpl"}
{assign var="fields_config" value=$fields_config scope=global}
{assign var="item" value=$item scope=global}
