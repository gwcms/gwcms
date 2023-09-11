




{function name="df_inputs"}
	
{if preg_match('/_HTML$/', $item->key)}
	{$contheight="150px"}
	{$conttype=htmlarea}
{else}
	{$contheight="50px"}
	{$conttype=textarea}
{/if}
	
	
	{*$dl_checklist_enabled=1*}
	<td></td>
	
	{foreach $m->list_config.dl_fields as $field}
		{$field}
		{if strpos($field, "value_")===0}
		
			{call e field=$field type=$conttype height=$contheight}
		{elseif $field=='module' || $field=='key'}
			{call e}
		{else}
			<td>{$item->$field}</td>
		{/if}

	{/foreach}

{/function}

{$layout=inline}
{$if_actions=[save]}
{include file="default_inline_form.tpl"} 