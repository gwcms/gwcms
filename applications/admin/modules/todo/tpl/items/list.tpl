{extends file="default_list.tpl"}

{block name="init"}
	
	{$dl_checklist_enabled=1}
	{$dl_inline_edit=1}
	{$dl_cl_actions=[invertactive,dialogremove]}	
	
	{capture append=footer_hidden}
		<style type="text/css">
		.row-inprogress td{ font-weight: bold; }
		.hoverfolder .fa:before {
			content: "\f07b";
		}
		.hoverfolder:hover .fa:before {
			content: "\f07c";
		}	
		.hoverfolder span{
			margin-left:2px
		}
		.hoverfolder .fa{
			vertical-align: middle;
		}
	</style>
	{/capture}



{$link=$app->fh()->gw_link([params=>[pid=>$item->id], path_only=>1])}

	{$users = $app->user->getOptions()}
	{function name=dl_prepare_item}
		{if $item->state==15}
			{$item->set('row_class', 'row-inprogress')}
		{/if}
	{/function}

	{function name=dl_cell_state_title}
		{GW::l("/m/STATE_OPT/{$item->state}")}
	{/function}
	
	{function name=dl_cell_title}
		{$link=$app->fh()->gw_link([params=>[pid=>$item->id], path_only=>1])}
		
		{if $item->type==1}
			<a href="{$link}" class="hoverfolder"><i class="fa text-mint fa-fw"></i><span>{$item->title} {$tmp=$item->child_count}{if $tmp}({$tmp}){/if}</span></a>
		{else}
			{$item->title}
		{/if}
	{/function}

	{function name=dl_cell_user_create}
				{$users[$item->user_create]}
	{/function}	
	
	
	{function name="actSwitchState"}
		{list_item_action_m 
			url=[false,[act=>doSwitchState,id=>$item->id,state=>$state]] 
			iconclass="fa `$addclass`" 
			action_addclass="ajax-link" 
			title=GW::l("/m/CHANGE_STATE_TO/{$state}")}
	{/function}

	{function name=dl_cell_state}
		
		{$states=GW::l('/m/STATE_OPT')|strip_tags}
		{$state_colors=[200=>red,100=>green,12=>yellow,50=>orange,15=>violet]}
		{$curr_color=$state_colors[$item->state]}
		
		
		
		{if $item->state<12}{$is_new=''}{else}{$is_new='text-muted1'}{/if}
		{if $item->state==15}{$is_running=''}{else}{$is_running='text-muted1'}{/if}
		{if $item->state==50}{$is_bug=''}{else}{$is_bug='text-muted1'}{/if}
		{if $item->state==12}{$is_paused=''}{else}{$is_paused='text-muted1'}{/if}
		{if $item->state==100}{$is_finished=''}{else}{$is_finished='text-muted1'}{/if}
		{if $item->state===200}{$is_canceled=''}{else}{$is_canceled='text-muted1'}{/if}
		

		
		{if $item->state >= 100}
			
			{actSwitchState state=50 addclass="fa-bug text-purple"}
			
			{if $item->state==100}
				{actSwitchState state=100 addclass="fa-check-circle `$is_finished` text-success"}
			{else}
				{actSwitchState state=200 addclass="fa-times-circle `$is_canceled` text-danger"}	
			{/if}
		{else}
			{*
			{gw_link do=switch_state params=[id=>$item->id,state=>15] icon="dot_violet`$is_violet`" title=$states.15 show_title=0}
			*}
			{actSwitchState state=10 addclass="fa-stop-circle text-primary `$is_new`"}
			{actSwitchState state=15 addclass="fa-play-circle text-success `$is_running`"}
			{actSwitchState state=12 addclass="fa-pause-circle `$is_paused` text-warning"}
			{actSwitchState state=50 addclass="fa-bug text-purple `$is_bug`"}
			{actSwitchState state=100 addclass="fa-check-circle `$is_finished` text-success"}
			{actSwitchState state=200 addclass="fa-times-circle `$is_canceled` text-danger"}						
		{/if}
		

	{/function}	
	

	{function name=dl_cell_user_exec}
				{$users[$item->user_exec]}
	{/function}
	{function name=dl_cell_deadline}
			{if $item->deadline!='0000-00-00 00:00:00' && $item->deadline!=''}
				{date('Y-m-d',strtotime($item->deadline))}
			{else}
				-
			{/if}
	{/function}	
	{function name=dl_cell_time_have}
			{if $item->time_have=='-1'}
				-
			{else}
				{gw_math_helper::uptime($item->time_have)}
			{/if}
	{/function}	
	{function name=dl_cell_project_id}
		
		{$p=$options.project[$item->project_id]}
		
		<span style="background-color:{$p->color};padding: 0 5px 0 5px;color:{$p->fcolor};border-radius: 3px;">{$p->title}</span>
			
	{/function}	
	{function name=dl_cell_week}
		<span title="{$item->insert_time}">W{date('W',strtotime($item->insert_time))}
		</span>
	{/function}	
	{function name=dl_cell_last_comment}
		{$item->last_comment|strip_tags|truncate:40}
		{$tmp=$item->comments_count}
		{if $tmp>1}<span class="text-muted" title="{GW::l('/m/FIELDS/comments')}">+{$tmp-1}</span>{/if}		
	{/function}
	
	
	{function name=dl_cell_attachments}
		{$tmp=$item->extensions.attachments->count()}	
		{if $tmp}{$tmp}{else}{/if}
	{/function}	
	
	
	{function name=dl_cell_description}
		{$item->description|strip_tags|truncate:40}
	{/function}	
		
	

	{$dl_smart_fields=[week,project_id,state,user_create,user_exec,title,deadline,time_have,last_comment,description,attachments]}
	{$dl_output_filters=[update_time=>short_time,insert_time=>short_time]}
	

	
	{$do_toolbar_buttons_hidden[] = dialogconf}	
	{$do_toolbar_buttons_hidden[] = print}
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[edit,delete_ajax]}
	
	
{/block}