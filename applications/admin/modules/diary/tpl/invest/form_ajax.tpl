{function name="df_inputs"}
	
	{*dl_checklist*}
	
	{foreach $m->list_config.dl_fields as $field}
		
		{if in_array($field, [sum,object,platform,strategy,take_profit,stop_loss,profit,termination_date])}
			{call e field=$field}
		{else}
			<td>{$item->get($field)}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 