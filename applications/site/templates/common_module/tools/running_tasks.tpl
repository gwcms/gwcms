{if isset($app->sess.bgtasks) && $app->sess.bgtasks}
	{GW_Background_Task::singleton()->checkExpired()}
	
<div class="mainnav-widget" id="backgroundTasks">

	<!-- Show the button on collapsed navigation -->
	<div class="show-small">
		<a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
			<i class="demo-pli-monitor-2"></i>
		</a>
	</div>

	<!-- Hide the content on collapsed navigation -->
	<div id="demo-wg-server" class="hide-small mainnav-widget-content">
		<ul class="list-group">
			<li class="list-header pad-no pad-ver">{GW::ln('/g/BACKGROUND_TASKS')}</li>
			
			{foreach $app->sess.bgtasks as $bgtask}
				
			<li class="mar-btm backgroundTask" id="backgroundtask_{$bgtask->id}" title="id: ">
				<span style="display:none" class="startTime">{$bgtask->start}</span>
				<span class="label label-primary pull-right timeGoing">{$bgtask->executionTime()}</span>
				<p>{$bgtask->title|truncate:30|escape}</p>
				{if $bgtask->expected_duration}
				<span style="display:none" class="expectedDuration">{$bgtask->expected_duration}</span>
				<div class="progress progress-sm">
					<div class="progress-bar progress-bar-mint" style="width:0%;">
						<span class="sr-only">0%</span>
					</div>
				</div>
				{/if}
			</li>
			{/foreach}
			{*
			<li class="pad-ver"><a href="#" class="btn btn-success btn-bock">View Details</a></li>
			*}
		</ul>
	</div>
</div>
		
		<script>
			require(['gwcms'], function(){
				gw_adm_sys.bgTaskRunCounters({time()})
			})
		</script>
		
		
{/if}