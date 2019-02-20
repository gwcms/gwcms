{function name="df_inputs"}
	

	{foreach $m->list_config.dl_fields as $field}
		
		{if in_array($field,[title,title_short])}
			
			{call e field=$field}
		{elseif in_array($field,[calculate,dropdown])}	
			{call e field=$field type=bool}
		{elseif in_array($field,[priority])}	
			{call e field=$field type=number}
		{elseif $field=="type"}
			{call e field=type type=select_plain options=$m->lang.OPTIONS.page_view_types}			
		{else}
			<td>{$item->get($field)}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 