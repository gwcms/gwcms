{capture assign=tmpformhtml}
	{include file="`$m->tpl_dir`elements.tpl"} 
{/capture}

{include file="default_form_open.tpl"}
{include "tools/form_components.tpl"}

{$tmpformhtml}



{if $fields_config}
	{call "build_form"}
{else}

	{foreach $m->list_config.dl_fields as $field}
		<tr>{call "cust_inputs"}</tr>
	{/foreach}
{/if}



{include file="default_form_close.tpl"}