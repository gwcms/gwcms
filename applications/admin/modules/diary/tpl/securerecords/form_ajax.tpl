{function name="df_inputs"}

	{*dl_checklist*}
	{*<td></td>*}
	
	{foreach $m->list_config.dl_fields as $field}
					
		{if in_array($field,[username,pass,comments])}
			
			{if $item->encrypted}
				{$tmp=base64_encode($item->get($field))}
				{call e field=$field type=read value=$tmp}
			{else}
				{call e field=$field type=text}
			{/if}
		{elseif $field=="title"}
			{call e field=$field type=text}
		{else}
			<td>{$item->get($field)}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 