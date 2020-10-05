{include "elements/input_func.tpl"}

{function build_input}
	{if $col==0 && !$smarty.get.form_ajax}
		<tr>
	{/if}

	{if $params_expand.colspan}
		{$col=$col+$params_expand.colspan}
		{$params_expand.colspan=$params_expand.colspan+1} {*+1 vienas value kitas title stulpeliai*}
	{else}
		{$col=$col+1}
	{/if}

	{if	$params_expand.value_from_var}
		{$tmpval=${$params_expand.value_from_var}}
	{else}
		{$tmpval=null}
	{/if}

	{if !$smarty.get.form_ajax}
		{$notr=true}
	{/if}

	{call e value=$tmpval}


	{if !$smarty.get.form_ajax && ($col >= $fields_config.cols || $col+$next.colspan-1 > $fields_config.cols)}
		</tr>
		{$col=0}
	{/if}	
{/function}

{function "build_form_normal"}
	{if $fields_config}

		{foreach $fields_config.fields as $field => $params_expand}

			{$next=next($fields_config.fields)}


			{call "build_input"}
			

		{/foreach}

	{/if}
{/function}


{function "build_form"}
	{if !$smarty.get.form_ajax}
		{call build_form_normal}
	{/if}
	
{/function}


{function "cust_inputs"}
	{if $fields_config.fields[$field]}
		{$params_expand=$fields_config.fields[$field]}


		{call "build_input"}
	{else}
		<td>{$item->$field}</td>
	{/if}
{/function}


