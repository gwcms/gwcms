{include file="default_form_open.tpl"}

{call e field=title i18n=3}
{call e field=description type=textarea height="100px"}
{call e field=active type=bool}


{*isplestiniai laukai*}
{$fields_config=[cols=>1,fields=>[]]}
{$m->addDynamicFieldsConfig($fields_config, $item)}



{include "tools/form_components.tpl"}
{call "build_form_normal"}
{*isplestiniai laukai*}

{include file="default_form_close.tpl"}