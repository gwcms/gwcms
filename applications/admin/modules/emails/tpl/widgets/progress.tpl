{if $messages_in_progres}
<div class="mainnav-widget">

	<!-- Show the button on collapsed navigation -->
	<div class="show-small">
		<a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
			<i class="demo-pli-monitor-2"></i>
		</a>
	</div>

	<!-- Hide the content on collapsed navigation -->
	<div id="demo-wg-server" class="hide-small mainnav-widget-content">
		<ul class="list-group">
			<li class="list-header pad-no pad-ver">{GW::l('/m/MAP/childs/messages/title')}</li>
			
			{foreach $messages_in_progres as $msg}
				{$progress=$msg->progress}
			<li class="mar-btm progessContainer" 
			    id="progess_massemail_{$msg->id}" 
			    data-updater-id="massemail-{$msg->id}" 
			    data-update-url="{$app->buildUri('emails/messages',[act=>doGetProgessPackets,id=>$msg->id,packets=>1])}">
				<span class="label label-primary pull-right valuedrop">{$progress}%</span>
				<p><span style="display:block;text-overflow: ellipsis; max-width: 150px;overflow: hidden;white-space: nowrap;"><a href="{$app->buildUri('emails/messages',[id=>$msg->id])}">{$msg->title|truncate:50|escape}</a></span></p>
				<div class="progress progress-sm">
					<div class="progress-bar progress-bar-purple" style="width: {$progress}%;">
						<span class="sr-only valuedrop">{$progress}%</span>
					</div>
				</div>
			</li>
			
			{/foreach}
			{*
			<li class="pad-ver"><a href="#" class="btn btn-success btn-bock">View Details</a></li>
			*}
		</ul>
	</div>
		
	<script>

		require(['gwcms'], function(){ 
			$('.progessContainer').each(function(){
				var o = $(this);
				gw_adm_sys.runUpdaters(o.attr('id'), o.data('update-url'), [], 30000); //30secs
			});
		});

	</script>		
</div>
	
			
			
			
			
			
{/if}



