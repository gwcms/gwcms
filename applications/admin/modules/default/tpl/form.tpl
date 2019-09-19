{capture assign=tmpformhtml}
	{include file="`$m->tpl_dir`elements.tpl"} 
{/capture}

{include file="default_form_open.tpl"}

{$tmpformhtml}

{foreach $m->list_config.dl_fields as $field}
	{call "cust_inputs"}
{/foreach}



{include file="default_form_close.tpl"}