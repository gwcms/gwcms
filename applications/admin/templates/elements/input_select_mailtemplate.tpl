
{$opts.no_idname=1}
{$tmp=$app->sess("email_templates/{$owner.owner_type}/{$owner.owner_field}/cfg", $opts)}


{if $default_vals}
	{$tmp=$app->sess("email_templates/{$owner.owner_type}/{$owner.owner_field}/{$name}/default_vals", $default_vals)}
{/if}

{$emltplurl=$app->buildUri('emails/email_templates', array_merge($owner,[name=>$name,byid=>1]))}



{*
{include file="elements/input_select_edit.tpl" type=select empty_option=1 options=false datasource=$emltplurl}
*}


{call e 
	after_input_f="editadd"
	type="select_ajax"
	object_title=GW::l('/M/emails/MAP/childs/email_templates/title')
	form_url=Navigator::buildURI($emltplurl,[clean=>2,dialog=>1],[path=>form])
	list_url=Navigator::buildURI($emltplurl,[clean=>2])
	datasource=Navigator::buildURI($emltplurl,[],[path=>options])
	preload=1
	minimuminputlength=1
	options=[]
}	