{include "elements/input_func.tpl"}
{function "build_form"}

{if $fields_config}
		
	{foreach $fields_config.fields as $name => $params_expand}
		
		{$next=next($fields_config.fields)}
		
		
		{if $col==0}
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
		
		
		{call e field=$name notr=true value=$tmpval}
		
		
		{if $col >= $fields_config.cols || $col+$next.colspan-1 > $fields_config.cols}
			</tr>
			{$col=0}
		{/if}
		
		
	{/foreach}
	
{/if}

{/function}
