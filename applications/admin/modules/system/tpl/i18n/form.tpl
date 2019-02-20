{include file="default_form_open.tpl"}

{$width_title="1%"}
{$width_input="99%"}

{call e field=path}
{call e field=pathname}
{call e field=views type=textarea value=$item->VIEWS  height=200px tabs=1}
{call e field=orders type=textarea value=$item->ORDERS  height=200px tabs=1}
{call e field=notes type=htmlarea}
{call e field=active type="bool"}



{$item->set('fields_str',str_replace('"','',json_encode($item->fields)))}

{$extra_fields=[in_menu,fields_str,id,insert_time,update_time,sync_time]}

{include file="default_form_close.tpl"}