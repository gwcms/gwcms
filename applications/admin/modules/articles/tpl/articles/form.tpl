{include file="default_form_open.tpl"}



{include 
	file="elements/input_select_edit.tpl" 
	name=group_id type=select 
	empty_option=1
	datasource=$app->buildUri('articles/groups')
	options=[]
}

{include file="elements/input.tpl" type=image name=image title=$lang.IMAGE}
{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" type=textarea name=short}

{include file="elements/input.tpl" type=htmlarea name=text}
{include file="elements/input.tpl" type=bool name=active}



{include file="default_form_close.tpl"}