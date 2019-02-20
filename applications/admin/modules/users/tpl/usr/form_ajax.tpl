{function name="df_inputs"}
	
	{*dl_checklist*}
	<td></td>
	{foreach $m->list_config.dl_fields as $field}
		

		{if in_array($field, ["email",'phone','name', 'surname'])}	
			{*neveikia composer inputas ant inline*}
			{call e field=$field type=text}
			
		{else}
			<td>{$item->get($field)}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 