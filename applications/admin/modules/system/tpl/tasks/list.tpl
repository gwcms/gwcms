{extends file="default_list.tpl"}


{block name="init"}

	{function name=do_toolbar_buttons_log}
			{toolbar_button title=Log iconclass='gwico-Console' onclick="gwcms.open_rtlogview('system.log'); return false"}
	{/function}	
	
	{function name=dl_actions_halt}
		{if $item->running > 0}
			{gw_link do="haltTask" icon="action_halt" params=[id=>$item->id,sigkill=>1] show_title=0}

			<a href="#" onclick="gwcms.open_rtlogview('task_{$item->id}.log'); return false">Realtime</a>
		{/if}
	{/function}
	
	{$dl_output_filters_truncate_size=70}
	{$dl_output_filters=[insert_time=>short_time, time=>short_time, finish_time=>short_time, output=>truncate]}
	
	{$fields=[
		id=>1,
		name=>1,
		output=>1,
		error_code=>1,
		error_msg=>1,
		running=>1,
		time=>1,
		finish_time=>1,
		insert_time=>1,
		speed=>1,
		update_time=>0
	]}
	
	{$dl_fields=$m->getDisplayFields($fields)}
	{$do_toolbar_buttons[] = dialogconf}	
	{$do_toolbar_buttons[] = log}	
	{$dl_smart_fields=[name,running]}
	
	{function dl_cell_name}
		{if $item->counts}
			{gw_link do=show_logs params=[task=>$item->name] title="`$item->name` (`$item->counts`)"}
		{else}
			{$item->name}
		{/if}
	{/function}
	
	{function dl_cell_running}
		{if $item->running > 0}
			PID: {$item->running}
		{else}
			{GW::l("/m/OPTIONS/running/{$item->running}")}
		{/if}
	{/function}	
	
	{$dl_actions=[edit,delete,halt]}
	
	{$dl_filters=[title=>1, name=>1, insert_time=>1, active=>[type=>select, options=>GW::l('/g/ACTIVE_OPT')]]}
	
	{$dl_order_enabled_fields=$dl_fields}
	
{/block}





{block name="after_list"}
	<br /><br />
	
	<table class="gwTable gwBGWhite">
		<tr>
			<th colspan=3>Dashboard</th>
		</tr>
		<tr>
			<th>Tasks list</th>
			<th>{GW::l('/g/ACTIONS')}</th>
			<th>Info</th>			
		</tr>		
	<tr>
	
	<td  valign="top">
	
	<table class="gwTable">
		<tr>
			<th>{GW::l('/m/TASK_NAME')}</th>
			<th>{GW::l('/g/ACTIONS')}</th>
		</tr>

	{foreach $tasks as $task}
		<tr>
			<td>{$task}</td>
			<td>
				{gw_link do=show_logs params=[task=>$task] title="Show logs"}
				{gw_link do=run_task params=[task=>$task] title="DebugRun!"}
				{gw_link do=run_task_direct params=[task=>$task] title="DirectRun"}
			</td>
		</tr>	
	{/foreach}
	
	</table>
	
	</td>
	
	
	<td valign="top">
		<ul style="margin-right:15px">
			{*<li>{gw_link do="halt_all" title="Halt All" confirm=1}</li>*}
			<li>{gw_link do="restart_system" title="Restart system process"}</li>
			<li>{gw_link do="remove_all" title="Empty task log"}</li>
			<li><a href="#show_proc" onclick="open_iframe({ url:GW.ln+'/'+GW.path+'/processes', title:this.innerHTML }); return false">Show Processes ({GW_Proc_Ctrl::getRunningProcesses()|count})</a></li>
		</ul>
	</td>	
	<td  valign="top">
		System process id: <a href="#show_log" onclick="open_rtlogview('system.log'); return false">{GW_App_System::getRunningPid()}</a>
	</td>		
	</tr>
	</table>

{/block}
