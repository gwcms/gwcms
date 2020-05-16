{function name="cust_inputs"}
	
	{if $item->status==7}{$tmpreadonly=1}{else}{$tmpreadonly=0}{/if}
	
	{if $field==''}
		
	{elseif $field=="title"}
		{call e readonly=$tmpreadonly}
	{elseif $field=="admin_note"}
		
		{call e type=textarea height="50px" hidden_note="Only admin can see note. Client can not"}		
	{elseif $field=="expires"}
		{call e readonly=$tmpreadonly}
	{elseif $field=="admin_id"}
		{if $tmpreadonly && $item->admin_username}
			{call e type=read field=admin_username}
		{else}
			{call e type=select_ajax modpath="users/usr"  preload=1 options=[]}
		{/if}
		
	{elseif $field=="customer_email"}
		{call e readonly=$tmpreadonly}
	{elseif $field=="amount"}
		{call e type=number step="0.01" note="(EUR)" readonly=$tmpreadonly}
	{elseif $field=="paytime"}
		{call e type=read}	
	{else}
		{if $smarty.get.form_ajax}
		<td>{$item->$field}</td>
		{/if}
	{/if}
		
{/function}