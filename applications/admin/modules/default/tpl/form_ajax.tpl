{include file="elements/input_func.tpl"} 
{include file="tools/form_components.tpl"} 
{include file="`$m->tpl_dir`elements.tpl"} 



{function name="df_inputs"}
	{*dl_checklist*}
	{if $smarty.get.checklist}<td></td>{/if}
	

	{foreach $m->list_config.dl_fields as $field}

		{call "cust_inputs"}

	{/foreach}
	
{/function}



{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"}