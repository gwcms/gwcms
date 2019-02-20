

{function name="df_inputs"}

	{foreach $m->list_config.dl_fields as $field}
		
		{if strpos($field, 'title')!==false}
			{call e field=$field}
		{elseif $file=='123'}
		{else}
			<td></td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 