{extends file="default_list.tpl"}


{block name="init"}
	
	{$display_fields=[email,time,status]}
	{$dl_smart_fields=['email','status']}
	
	{$dl_fields=$display_fields}
	{$dl_toolbar_buttons = ['filters']}	
	
	{$dl_actions=[]}
	
	{$dl_filters=[name=>1, surname=>1, status=>1, time=>1]}
	
	
	{function dl_cell_email}		
		{$item->name} {$item->surname}
		{if $item->subscriber}
			<a href="{$app->ln}/{$m->module_path.0}/subscribers?id={$item->id}">{$item->email}</a>
		{else}
			{if strpos($item->email,'@mailinator.com')}
				<a href='http://mailinator.com/inbox.jsp?to={str_replace('@mailinator.com','',$item->email)}' target='_blank'>{$item->email}</a>
			{else}
				{$item->email}
			{/if}
		{/if}
	{/function}
	
	{function dl_cell_status}			
		{if $item->status}
			<span style="color:green">{$lang.YES}</span>
		{else}
			<span style="color:red">{$lang.NO}</span>
		{/if}
	{/function}	
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
{/block}