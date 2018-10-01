{include file="default_form_open.tpl" form_width="1000px"}



{include 
	file="elements/input_select_edit.tpl" 
	name=group_id type=select 
	empty_option=1
	datasource=$app->buildUri('articles/groups')
	options=[]
}

{include file="elements/input.tpl" type=image name=image title=$lang.IMAGE}
{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" type=textarea name=short height=70px}

{include file="elements/input.tpl" type=htmlarea name=text layout=wide}
{include file="elements/input.tpl" type=bool name=active}



{include file="default_form_close.tpl"}