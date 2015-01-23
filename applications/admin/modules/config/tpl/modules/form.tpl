{include file="default_form_open.tpl"}

{$width_title="1%"}
{$width_input="99%"}

{include file="elements/input.tpl" name=path}
{include file="elements/input.tpl" name=pathname}
{include file="elements/input.tpl" name=views type=json  height=200px tabs=1}
{include file="elements/input.tpl" name=notes type=htmlarea}
{include file="elements/input.tpl" name=active type="bool"}



{$item->set('fields_str',str_replace('"','',json_encode($item->fields)))}

{$extra_fields=[info,in_menu,fields_str,id,insert_time,update_time,sync_time]}

{include file="default_form_close.tpl"}