{include file="default_form_open.tpl" form_width="1000px"}



{include 
	file="elements/input_select_edit.tpl" 
	name=group_id type=select 
	empty_option=1
	datasource=$app->buildUri('articles/groups')
	options=[]
}

{call e field=image  type=imagetitle=$lang.IMAGE}
{call e field=title}
{call e field=short type=textarea height=70px}

{call e field=text type=htmlarea layout=wide}
{call e field=active type=bool}



{include file="default_form_close.tpl"}