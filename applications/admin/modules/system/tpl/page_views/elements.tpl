


{function name="cust_inputs"}
	
		{if in_array($field,[title,title_short])}
			{call e}
		{elseif in_array($field,[calculate,dropdown])}	
			{call e type=bool}
		{elseif in_array($field,[priority])}	
			{call e type=number}
		{elseif $field=="type"}
			{call e type=select_plain options=$m->lang.OPTIONS.page_view_types}	
		{elseif $field=="priority" || $field=="page_by"}
			{call e type=number}				
		{elseif $field=="fields" || $field=="condition"}	
			<td>...</td>
		{else}
			<td>{$item->get($field)}</td>
		{/if}
		
{/function}