

{function name="df_inputs"}
	{*$dl_checklist_enabled=1*}
	<td></td>
	
	{foreach $m->list_config.dl_fields as $field}
		{$field}
		{if strpos($field, "value_")===0}
		
			{include file="elements/input.tpl" name=$field type=textarea height="50px"}
		
		{else}
			<td>{$item->$field}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 