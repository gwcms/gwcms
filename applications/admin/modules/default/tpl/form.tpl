{include file="default_form_open.tpl"}
{include file="`$m->tpl_dir`elements.tpl"} 



{foreach $m->list_config.dl_fields as $field}
	{call "cust_inputs"}
{/foreach}



{include file="default_form_close.tpl"}