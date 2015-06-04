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

	{function name=dl_cell_state_title}
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
	

	{function name=dl_cell_state}
		
		{$states=$m->lang.STATE_OPT|strip_tags}
		{$state_colors=[200=>red,100=>green,12=>yellow,50=>orange,15=>violet]}
		{$curr_color=$state_colors[$item->state]}
		
		
		
		{if $item->state<12}{$is_white=''}{else}{$is_white='_0'}{/if}
		{if $item->state==15}{$is_violet=''}{else}{$is_violet='_0'}{/if}
		{if $item->state==50}{$is_orange=''}{else}{$is_orange='_0'}{/if}
		{if $item->state==12}{$is_yellow=''}{else}{$is_yellow='_0'}{/if}
		{if $item->state==100}{$is_green=''}{else}{$is_green='_0'}{/if}
		{if $item->state===200}{$is_red=''}{else}{$is_red='_0'}{/if}
		
		


		{if $item->state >= 100}
			{gw_link do=switch_state params=[id=>$item->id,state=>50] icon="dot_orange`$is_orange`" title=$states.50 show_title=0}
			
			
			{gw_link do=switch_state params=[id=>$item->id,state=>$item->state] icon="dot_`$curr_color`" title=$states[$item->state] show_title=0}
		{else}
			{gw_link do=switch_state params=[id=>$item->id,state=>10] icon="dot_white`$is_white`" title=$states.5 show_title=0}
			{gw_link do=switch_state params=[id=>$item->id,state=>15] icon="dot_violet`$is_violet`" title=$states.15 show_title=0}

			{gw_link do=switch_state params=[id=>$item->id,state=>12] icon="dot_yellow`$is_yellow`" title=$states.12 show_title=0}

			{gw_link do=switch_state params=[id=>$item->id,state=>100] icon="dot_green`$is_green`" title=$states.100 show_title=0}
			{gw_link do=switch_state params=[id=>$item->id,state=>200] icon="dot_red`$is_red`" title=$states.200 show_title=0}			
		{/if}
		

	{/function}	
	

	{function name=dl_cell_user_exec}
				{$users[$item->user_exec]}
	{/function}
	{function name=dl_cell_deadline}
			{if $item->deadline!='0000-00-00 00:00:00'}
				{date('Y-m-d',strtotime($item->deadline))}
			{else}
				-
			{/if}
	{/function}	
	

	{$dl_smart_fields=[state,user_create,user_exec,title,deadline]}
	
	
	{$display_fields = 	[
			id=>1,
			priority=>1,
			title=>1,
			deadline=>1,
			insert_time=>1,
			update_time=>1,
			user_create=>1,
			user_exec=>1,
			state=>1
		]}	
	
	{$dl_fields=$m->getDisplayFields($display_fields)}
	
	{$dl_toolbar_buttons[] = dialogconf}
	
	{$dl_actions=[edit, delete]}
	
	
	{$dl_filters=$display_fields}
	
	{$dl_order_enabled_fields=array_keys($display_fields)}
	
{/block}	