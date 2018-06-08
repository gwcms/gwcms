
{$opts.no_idname=1}
{$tmp=$app->sess("email_templates/{$owner.owner_type}/{$owner.owner_field}/cfg", $opts)}


{if $default_vals}
	{$tmp=$app->sess("email_templates/{$owner.owner_type}/{$owner.owner_field}/{$name}/default_vals", $default_vals)}
{/if}

{$emltplurl=$app->buildUri('emails/email_templates', array_merge($owner,[name=>$name,byid=>1]))}



{include file="elements/input_select_edit.tpl" type=select empty_option=1 options=false datasource=$emltplurl}