{include "default_form_open.tpl"}



<th colspan='2'>{GW::l('/g/VIEWS/config')}</th>

{call e field=blocks_filter_by_site type=bool}

{call e field="additfields" type=multiselect options=GW::l('/m/OPTIONS/additfields') value_format=json1}



<th colspan='2'>{$app->user->title} {GW::l('/g/VIEWS/config')}</th>
{call e field="user/{$app->user->id}/editor" type=select empty_option=1 options=GW::l('/m/OPTIONS/body_editor')}

{$hopts=["200px","300px","400px","500px","600px","700px","800px","900px","1000px","1100px","1200px","1300px"]}
{call e field="user/{$app->user->id}/editor_height" type=select empty_option=1 options=$hopts options_fix=1}

{include file="default_form_close.tpl" submit_buttons=[save]}


