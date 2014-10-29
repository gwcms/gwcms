{extends file="default_list.tpl"}

{block name="init"}


{*Laikinas reikalas*}
	{*Little hack for server to display current task*}
	{if $smarty.server.HTTP_HOST=="localhost"}
		<script>
			setTimeout('location=location',60000);
		</script>
	{/if}
{*Laikinas reikalas END*}

{$link=$app->fh()->gw_link([params=>[pid=>$item->id], path_only=>1])}

	{$users = $app->user->getOptions()}

	{function name=dl_cell_state}
				{$m->lang.STATE_OPT[$item->state]}
	{/function}
	
	{function name=dl_cell_title}
		{$link=$app->fh()->gw_link([params=>[pid=>$item->id], path_only=>1])}
		
		{if $item->type==1}
			<a href="{$link}">{$item->title} (Enter)</a>
		{else}
			{$item->title}
		{/if}
	{/function}

	{function name=dl_cell_user_create}
				{$users[$item->user_create]}
	{/function}	
	

	{function name=dl_cell_actions1}
		{if $item->user_exec == $app->user->id}
			{if ((int)$item->state < 100)}
				{gw_link do=complete params=[id=>$item->id] title="Fix"}
			{/if}
		{else}
			{gw_link do=execute params=[id=>$item->id] title="Exec"}
		{/if}
	{/function}	
	

	{function name=dl_cell_user_exec}
				{$users[$item->user_exec]}
	{/function}	

	{$dl_smart_fields=[state,user_create,user_exec,title,actions1]}
	
	
	{$display_fields = 	[
			id=>1,
			priority=>1,
			title=>1,
			state=>1,
			deadline=>1,
			insert_time=>1,
			update_time=>1,
			user_create=>1,
			user_exec=>1,
			actions1=>1
		]}	
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_toolbar_buttons[] = dialogconf}
	
	{$dl_actions=[edit, delete]}
	
	
	{$dl_filters=$display_fields}
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
	
{/block}	