{include file="default_form_open.tpl" form_width="100%"}

{$width_title=100px}

{include file="elements/input.tpl" name=path}
{include file="elements/input.tpl" name=pathname}

{include file="elements/input.tpl"  name=views type=code_json height=200px nopading=1}  
{include file="elements/input.tpl"  name=orders type=code_json height=200px nopading=1}  


{$ck_options=[toolbarStartupExpanded=>false]}
{include file="elements/input.tpl" name=notes type=htmlarea width="100%"}
{include file="elements/input.tpl" name=active type="bool"}



{$item->set('fields_str',str_replace('"','',json_encode($item->fields)))}

{$extra_fields=[in_menu,fields_str,id,insert_time,update_time,sync_time]}

{include file="default_form_close.tpl"}