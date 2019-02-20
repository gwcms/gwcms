

{function name="df_inputs"}

	{foreach $m->list_config.dl_fields as $field}
		
		{if $field=='title'}
			{call e field=title}
		{elseif $file=='123'}
		{else}
			<td></td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 